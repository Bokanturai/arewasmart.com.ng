<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Jobs\ProcessAISupportReply;


class SupportController extends Controller
{
    /**
     * Handle support messages from the main contact form.
     */
    public function sendContactMessage(Request $request)
    {
        if ($request->filled('honeypot_field')) {
            return back()->with('success', 'Thank you for your message!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if (Auth::check()) {
            $reference = 'TKT-' . strtoupper(Str::random(8));
            
            $chat = AiChat::create([
                'user_id' => Auth::id(),
                'reference' => $reference,
                'type' => 'support',
                'subject' => $request->subject,
                'status' => 'open',
                'role' => 'user',
                'content' => $request->message,
            ]);

            // For support reply, we pass the reference or the first message
            ProcessAISupportReply::dispatchSync($chat);

            return back()->with('success', 'Your message has been sent. A support ticket (#' . $reference . ') has been created for you.');
        }

        // For guest users, send an email to the admin
        try {
            \Illuminate\Support\Facades\Mail::to(config('mail.from.address'))
                ->send(new \App\Mail\GuestContactMail([
                    'name' => $request->name,
                    'email' => $request->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                ]));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Guest Contact Email Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you for contacting us! Our team will review your message and get back to you soon.');
    }

    /**
     * Display a listing of the user's support tickets.
     */
    public function index()
    {
        // Get unique tickets (rows with subject)
        $tickets = AiChat::support()
            ->where('user_id', Auth::id())
            ->whereNotNull('subject')
            ->latest()
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new support ticket.
     */
    public function create()
    {
        return view('support.create');
    }

    /**
     * Store a newly created support ticket in the database.
     */
    public function store(Request $request)
    {
        if ($request->filled('honeypot_field')) {
            return response()->json(['success' => true, 'message' => 'Ticket created successfully!']);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $reference = 'TKT-' . strtoupper(Str::random(8));
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support', 'public');
        }

        $chat = AiChat::create([
            'user_id' => Auth::id(),
            'reference' => $reference,
            'type' => 'support',
            'subject' => $request->subject,
            'status' => 'open',
            'role' => 'user',
            'content' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        ProcessAISupportReply::dispatchSync($chat);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully!',
                'redirect_url' => route('support.show', $reference)
            ]);
        }

        return redirect()->route('support.show', $reference)
            ->with('success', 'Ticket created successfully! Ticket ID: ' . $reference);
    }

    /**
     * Display the specified support ticket and its message thread.
     */
    public function show($reference)
    {
        $ticketHead = AiChat::support()
            ->where('reference', $reference)
            ->where('user_id', Auth::id())
            ->whereNotNull('subject')
            ->firstOrFail();

        $messages = AiChat::support()
            ->where('reference', $reference)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('support.show', [
            'ticket' => $ticketHead,
            'messages' => $messages
        ]);
    }

    /**
     * Process a reply to an existing support ticket.
     */
    public function reply(Request $request, $reference)
    {
        $ticketHead = AiChat::support()
            ->where('reference', $reference)
            ->where('user_id', Auth::id())
            ->whereNotNull('subject')
            ->firstOrFail();

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support', 'public');
        }

        $message = AiChat::create([
            'user_id' => Auth::id(),
            'reference' => $reference,
            'type' => 'support',
            'role' => 'user',
            'content' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        // Update the status of the entire thread (by updating the head record)
        $ticketHead->update(['status' => 'customer_reply', 'updated_at' => now()]);

        ProcessAISupportReply::dispatchSync($message);
  
        if ($request->ajax() || $request->wantsJson()) {
            $aiReply = AiChat::support()
                ->where('reference', $reference)
                ->where('role', 'assistant')
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'message' => $message,
                'ai_reply' => $aiReply,
                'ticket_status' => $ticketHead->status
            ]);
        }
  
        return back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Fetch real-time updates for a specific support thread.
     */
    public function fetchUpdates(Request $request, $reference)
    {
        $lastMessageId = $request->input('last_message_id', 0);
        
        $messages = AiChat::support()
            ->where('reference', $reference)
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();

        $isTyping = \Illuminate\Support\Facades\Cache::get('admin_typing_' . $reference, false);

        return response()->json([
            'messages' => $messages,
            'is_typing' => $isTyping,
        ]);
    }

    /**
     * Close a support ticket.
     */
    public function close($reference)
    {
        $ticketHead = AiChat::support()
            ->where('reference', $reference)
            ->where('user_id', Auth::id())
            ->whereNotNull('subject')
            ->firstOrFail();

        $ticketHead->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully.'
        ]);
    }
}
