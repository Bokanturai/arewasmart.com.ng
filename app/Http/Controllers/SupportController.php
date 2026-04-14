<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessAISupportReply;


class SupportController extends Controller
{
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

        // If user is logged in, create a ticket
        if (Auth::check()) {
            $ticket = SupportTicket::create([
                'user_id' => Auth::id(),
                'ticket_reference' => 'TKT-' . strtoupper(Str::random(8)),
                'subject' => $request->subject,
                'status' => 'open',
                'priority' => 'medium',
            ]);

            SupportMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'is_admin_reply' => false,
            ]);

            // Trigger AI Support Reply Synchronously (No Queue)
            ProcessAISupportReply::dispatchSync($ticket);

            return back()->with('success', 'Your message has been sent. A support ticket (#' . $ticket->ticket_reference . ') has been created for you.');
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
            // We still return success to the user as they've done their part, 
            // but log the error for admin investigation.
        }

        return back()->with('success', 'Thank you for contacting us! Our team will review your message and get back to you soon.');
    }

    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

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

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_reference' => 'TKT-' . strtoupper(Str::random(8)),
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support', 'public');
        }

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
            'is_admin_reply' => false,
        ]);

        // Trigger AI Support Reply Synchronously (No Queue)
        ProcessAISupportReply::dispatchSync($ticket);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully!',
                'redirect_url' => route('support.show', $ticket->ticket_reference)
            ]);
        }

        return redirect()->route('support.show', $ticket->ticket_reference)
            ->with('success', 'Ticket created successfully! Ticket ID: ' . $ticket->ticket_reference);
    }

    public function show($reference)
    {
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', Auth::id())
            ->with('messages.user')
            ->firstOrFail();

        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, $reference)
    {
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support', 'public');
        }

        $message = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
            'is_admin_reply' => false,
        ]);

        $ticket->update(['status' => 'customer_reply', 'updated_at' => now()]);

        // Trigger AI Support Reply Synchronously (No Queue)
        ProcessAISupportReply::dispatchSync($ticket);
 
        if ($request->ajax() || $request->wantsJson()) {
            // Eager load user for the response
            $message = SupportMessage::with('user')->find($message->id);
            
            // Get the AI reply if it exists (since we ran synchronously)
            $aiReply = SupportMessage::where('support_ticket_id', $ticket->id)
                ->where('is_admin_reply', true)
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'message' => $message,
                'ai_reply' => $aiReply,
                'ticket_status' => $ticket->status
            ]);
        }
 
        return back()->with('success', 'Reply sent successfully.');
    }


    public function fetchUpdates(Request $request, $reference)
    {
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check for new messages since the last loaded message ID
        $lastMessageId = $request->input('last_message_id', 0);
        
        $messages = SupportMessage::where('support_ticket_id', $ticket->id)
            ->where('id', '>', $lastMessageId)
            ->with('user') 
            ->orderBy('created_at', 'asc')
            ->get();

       
        // Key format assumption: admin_typing_TICKETID
        $isTyping = \Illuminate\Support\Facades\Cache::get('admin_typing_' . $ticket->id, false);

        return response()->json([
            'messages' => $messages,
            'is_typing' => $isTyping,
        ]);
    }

    public function close($reference)
    {
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $ticket->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully.'
        ]);
    }
}
