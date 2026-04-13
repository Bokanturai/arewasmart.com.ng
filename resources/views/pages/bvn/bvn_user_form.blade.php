@csrf

<!-- Service Field -->
<div class="col-md-6">
    <label class="form-label fw-semibold">Select Enrollment Bank <span class="text-danger">*</span></label>
    <select class="form-select" name="service_field_id" id="service_field" required>
        <option value="">-- Select Service Field --</option>
        @foreach ($serviceFields as $field)
            @php
                $price = $field->getPriceForUserType(auth()->user()->role);
            @endphp
            <option value="{{ $field->id }}"
                data-price="{{ $price }}"
                data-description="{{ $field->description }}"
                {{ old('service_field_id') == $field->id ? 'selected' : '' }}>
                {{ $field->field_name }} - ₦{{ number_format($price, 2) }}
            </option>
        @endforeach
    </select>
    <small class="text-muted d-block mt-1" id="field-description"></small>
    <div class="invalid-feedback">Please select a service field.</div>
</div>

<!-- Personal Information Section -->
<div class="col-12 mt-4">
    <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">
        <i class="bi bi-person-vcard me-2"></i>Personal Information
    </h6>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
    <input class="form-control" name="first_name" type="text" required
        placeholder="Enter your first name" value="{{ old('first_name') }}">
    <div class="invalid-feedback">Please provide your first name.</div>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
    <input class="form-control" name="last_name" type="text" required
        placeholder="Enter your last name" value="{{ old('last_name') }}">
    <div class="invalid-feedback">Please provide your last name.</div>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">Middle Name</label>
    <input class="form-control" name="middle_name" type="text"
        placeholder="Enter your middle name" value="{{ old('middle_name') }}">
</div>

<!-- Banking Information Section -->
<div class="col-12 mt-4">
    <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">
        <i class="bi bi-bank me-2"></i>Banking Information
    </h6>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold" for="bvn_field">BVN <span class="text-danger">*</span></label>
    <input class="form-control numeric-only" id="bvn_field" name="bvn" type="text" minlength="11" maxlength="11" required
        placeholder="Enter your 11-digit BVN" value="{{ old('bvn') }}"
        inputmode="numeric" pattern="\d{11}" aria-label="11-digit BVN">
    <div class="invalid-feedback">Please provide a valid 11-digit BVN.</div>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold" for="account_no">Account Number <span class="text-danger">*</span></label>
    <input class="form-control numeric-only" id="account_no" name="account_no" type="text" minlength="10" maxlength="10" required
        placeholder="Enter your 10-digit account number" value="{{ old('account_no') }}"
        inputmode="numeric" pattern="\d{10}" aria-label="10-digit Account Number">
    <div class="invalid-feedback">Please provide a valid 10-digit account number.</div>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
    <input class="form-control" name="bank_name" type="text" required
        placeholder="Enter your bank name" value="{{ old('bank_name') }}">
    <div class="invalid-feedback">Please provide your bank name.</div>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Account Name <span class="text-danger">*</span></label>
    <input class="form-control" name="account_name" type="text" required
        placeholder="Enter your account name" value="{{ old('account_name') }}">
    <div class="invalid-feedback">Please provide your account name.</div>
</div>

<!-- Contact Information Section -->
<div class="col-12 mt-4">
    <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">
        <i class="bi bi-telephone me-2"></i>Contact Information
    </h6>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
    <input class="form-control" name="email" type="email" required
        placeholder="Enter your email address" value="{{ old('email') }}">
    <div class="invalid-feedback">Please provide a valid email address.</div>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold" for="phone_no">Phone Number <span class="text-danger">*</span></label>
    <input class="form-control numeric-only" id="phone_no" name="phone_no" type="tel" minlength="11" maxlength="11" required
        placeholder="Enter your 11-digit phone number" value="{{ old('phone_no') }}"
        inputmode="tel" pattern="\d{11}" aria-label="11-digit Phone Number">
    <div class="invalid-feedback">Please provide a valid 11-digit phone number.</div>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
    <input class="form-control" name="dob" type="date" required value="{{ old('dob') }}">
    <div class="invalid-feedback">Please select your date of birth.</div>
</div>

<!-- Location Information Section -->
<div class="col-12 mt-4">
    <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">
        <i class="bi bi-geo-alt me-2"></i>Location Information
    </h6>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
    <select class="form-select" id="state" name="state" required>
        <option value="" disabled selected>Choose State</option>
        <option value="Abia">Abia</option>
        <option value="Adamawa">Adamawa</option>
        <option value="Akwa Ibom">Akwa Ibom</option>
        <option value="Anambra">Anambra</option>
        <option value="Bauchi">Bauchi</option>
        <option value="Bayelsa">Bayelsa</option>
        <option value="Benue">Benue</option>
        <option value="Borno">Borno</option>
        <option value="Cross River">Cross River</option>
        <option value="Delta">Delta</option>
        <option value="Ebonyi">Ebonyi</option>
        <option value="Edo">Edo</option>
        <option value="Ekiti">Ekiti</option>
        <option value="Enugu">Enugu</option>
        <option value="FCT">Federal Capital Territory</option>
        <option value="Gombe">Gombe</option>
        <option value="Imo">Imo</option>
        <option value="Jigawa">Jigawa</option>
        <option value="Kaduna">Kaduna</option>
        <option value="Kano">Kano</option>
        <option value="Katsina">Katsina</option>
        <option value="Kebbi">Kebbi</option>
        <option value="Kogi">Kogi</option>
        <option value="Kwara">Kwara</option>
        <option value="Lagos">Lagos</option>
        <option value="Nasarawa">Nasarawa</option>
        <option value="Niger">Niger</option>
        <option value="Ogun">Ogun</option>
        <option value="Ondo">Ondo</option>
        <option value="Osun">Osun</option>
        <option value="Oyo">Oyo</option>
        <option value="Plateau">Plateau</option>
        <option value="Rivers">Rivers</option>
        <option value="Sokoto">Sokoto</option>
        <option value="Taraba">Taraba</option>
        <option value="Yobe">Yobe</option>
        <option value="Zamfara">Zamfara</option>
    </select>
    <div class="invalid-feedback">Please select your state.</div>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">LGA <span class="text-danger">*</span></label>
    <select class="form-select" id="lga" name="lga" required disabled>
        <option value="" disabled selected>Select State First</option>
    </select>
    <div class="invalid-feedback">Please select your LGA.</div>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">Shop Address <span class="text-danger">*</span></label>
    <input class="form-control" id="address" name="address" type="text" required
        placeholder="NO091 Tudun wada birnin kebbi" value="{{ old('address') }}">
    <div class="invalid-feedback">Please provide your shop address.</div>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Agent Location <span class="text-danger">*</span></label>
    <input class="form-control" id="agent_location" name="agent_location" type="text" required
        placeholder="UZOGU VILLAGE" value="{{ old('agent_location') }}">
    <div class="invalid-feedback">Please provide agent location.</div>
</div>

<script>
// Comprehensive list of all Nigerian states and their LGAs
const lgas = {
    "Abia": ["Aba North", "Aba South", "Arochukwu", "Bende", "Ikwuano", "Isiala Ngwa North", "Isiala Ngwa South", "Isuikwuato", "Obi Ngwa", "Ohafia", "Osisioma", "Ugwunagbo", "Ukwa East", "Ukwa West", "Umuahia North", "Umuahia South", "Umu Nneochi"],
    "Adamawa": ["Demsa", "Fufore", "Ganye", "Girei", "Gombi", "Guyuk", "Hong", "Jada", "Lamurde", "Madagali", "Maiha", "Mayo Belwa", "Michika", "Mubi North", "Mubi South", "Numan", "Shelleng", "Song", "Toungo", "Yola North", "Yola South"],
    "Akwa Ibom": ["Abak", "Eastern Obolo", "Eket", "Esit Eket", "Essien Udim", "Etim Ekpo", "Etinan", "Ibeno", "Ibesikpo Asutan", "Ibiono Ibom", "Ika", "Ikono", "Ikot Abasi", "Ikot Ekpene", "Ini", "Itu", "Mbo", "Mkpat Enin", "Nsit Atai", "Nsit Ibom", "Nsit Ubium", "Obot Akara", "Okobo", "Onna", "Oron", "Oruk Anam", "Udung Uko", "Ukanafun", "Uruan", "Urue-Offong/Oruko", "Uyo"],
    "Anambra": ["Aguata", "Anambra East", "Anambra West", "Anaocha", "Awka North", "Awka South", "Ayamelum", "Dunukofia", "Ekwusigo", "Idemili North", "Idemili South", "Ihiala", "Njikoka", "Nnewi North", "Nnewi South", "Ogbaru", "Onitsha North", "Onitsha South", "Orumba North", "Orumba South", "Oyi"],
    "Bauchi": ["Alkaleri", "Bauchi", "Bogoro", "Damban", "Darazo", "Dass", "Gamawa", "Ganjuwa", "Giade", "Itas/Gadau", "Jama'are", "Katagum", "Kirfi", "Misau", "Ningi", "Shira", "Tafawa Balewa", "Toro", "Warji", "Zaki"],
    "Bayelsa": ["Brass", "Ekeremor", "Kolokuma/Opokuma", "Nembe", "Ogbia", "Sagbama", "Southern Ijaw", "Yenagoa"],
    "Benue": ["Ado", "Agatu", "Apa", "Buruku", "Gboko", "Guma", "Gwer East", "Gwer West", "Katsina-Ala", "Konshisha", "Kwande", "Logo", "Makurdi", "Obi", "Ogbadibo", "Ohimini", "Oju", "Okpokwu", "Otukpo", "Tarka", "Ukum", "Ushongo", "Vandeikya"],
    "Borno": ["Abadam", "Askira/Uba", "Bama", "Bayo", "Biu", "Chibok", "Damboa", "Dikwa", "Gubio", "Guzamala", "Gwoza", "Hawul", "Jere", "Kaga", "Kala/Balge", "Konduga", "Kukawa", "Kwaya Kusar", "Mafa", "Magumeri", "Maiduguri", "Marte", "Mobbar", "Monguno", "Ngala", "Nganzai", "Shani"],
    "Cross River": ["Abi", "Akamkpa", "Akpabuyo", "Bakassi", "Bekwarra", "Biase", "Boki", "Calabar Municipal", "Calabar South", "Etung", "Ikom", "Obanliku", "Obubra", "Obudu", "Odukpani", "Ogoja", "Yakurr", "Yala"],
    "Delta": ["Aniocha North", "Aniocha South", "Bomadi", "Burutu", "Ethiope East", "Ethiope West", "Ika North East", "Ika South", "Isoko North", "Isoko South", "Ndokwa East", "Ndokwa West", "Okpe", "Oshimili North", "Oshimili South", "Patani", "Sapele", "Udu", "Ughelli North", "Ughelli South", "Ukwuani", "Uvwie", "Warri North", "Warri South", "Warri South West"],
    "Ebonyi": ["Abakaliki", "Afikpo North", "Afikpo South", "Ebonyi", "Ezza North", "Ezza South", "Ikwo", "Ishielu", "Ivo", "Izzi", "Ohaozara", "Ohaukwu", "Onicha"],
    "Edo": ["Akoko-Edo", "Egor", "Esan Central", "Esan North-East", "Esan South-East", "Esan West", "Etsako Central", "Etsako East", "Etsako West", "Igueben", "Ikpoba-Okha", "Oredo", "Orhionmwon", "Ovia North-East", "Ovia South-West", "Owan East", "Owan West", "Uhunmwonde"],
    "Ekiti": ["Ado Ekiti", "Efon", "Ekiti East", "Ekiti South-West", "Ekiti West", "Emure", "Gbonyin", "Ido Osi", "Ijero", "Ikere", "Ikole", "Ilejemeje", "Irepodun/Ifelodun", "Ise/Orun", "Moba", "Oye"],
    "Enugu": ["Aninri", "Awgu", "Enugu East", "Enugu North", "Enugu South", "Ezeagu", "Igbo Etiti", "Igbo Eze North", "Igbo Eze South", "Isi Uzo", "Nkanu East", "Nkanu West", "Nsukka", "Oji River", "Udenu", "Udi", "Uzo Uwani"],
    "FCT": ["Abaji", "Bwari", "Gwagwalada", "Kuje", "Kwali", "Municipal Area Council"],
    "Gombe": ["Akko", "Balanga", "Billiri", "Dukku", "Funakaye", "Gombe", "Kaltungo", "Kwami", "Nafada", "Shongom", "Yamaltu/Deba"],
    "Imo": ["Aboh Mbaise", "Ahiazu Mbaise", "Ehime Mbano", "Ezinihitte", "Ideato North", "Ideato South", "Ihitte/Uboma", "Ikeduru", "Isiala Mbano", "Isu", "Mbaitoli", "Ngor Okpala", "Njaba", "Nkwerre", "Nwangele", "Obowo", "Oguta", "Ohaji/Egbema", "Okigwe", "Orlu", "Orsu", "Oru East", "Oru West", "Owerri Municipal", "Owerri North", "Owerri West"],
    "Jigawa": ["Auyo", "Babura", "Biriniwa", "Birnin Kudu", "Buji", "Dutse", "Gagarawa", "Garki", "Gumel", "Guri", "Gwaram", "Gwiwa", "Hadejia", "Jahun", "Kafin Hausa", "Kaugama", "Kazaure", "Kiri Kasama", "Kiyawa", "Maigatari", "Malam Madori", "Miga", "Ringim", "Roni", "Sule Tankarkar", "Taura", "Yankwashi"],
    "Kaduna": ["Birnin Gwari", "Chikun", "Giwa", "Igabi", "Ikara", "Jaba", "Jema'a", "Kachia", "Kaduna North", "Kaduna South", "Kagarko", "Kajuru", "Kaura", "Kauru", "Kubau", "Kudan", "Lere", "Makarfi", "Sabon Gari", "Sanga", "Soba", "Zangon Kataf", "Zaria"],
    "Kano": ["Ajingi", "Albasu", "Bagwai", "Bebeji", "Bichi", "Bunkure", "Dala", "Dambatta", "Dawakin Kudu", "Dawakin Tofa", "Doguwa", "Fagge", "Gabasawa", "Garko", "Garun Mallam", "Gaya", "Gezawa", "Gwale", "Gwarzo", "Kabo", "Kano Municipal", "Karaye", "Kibiya", "Kiru", "Kumbotso", "Kunchi", "Kura", "Madobi", "Makoda", "Minjibir", "Nasarawa", "Rano", "Rimin Gado", "Rogo", "Shanono", "Sumaila", "Takai", "Tarauni", "Tofa", "Tsanyawa", "Tudun Wada", "Ungogo", "Warawa", "Wudil"],
    "Katsina": ["Bakori", "Batagarawa", "Batsari", "Baure", "Bindawa", "Charanchi", "Dandume", "Danja", "Dan Musa", "Daura", "Dutsi", "Dutsin Ma", "Faskari", "Funtua", "Ingawa", "Jibia", "Kafur", "Kaita", "Kankara", "Kankia", "Katsina", "Kurfi", "Kusada", "Mai'Adua", "Malumfashi", "Mani", "Mashi", "Matazu", "Musawa", "Rimi", "Sabuwa", "Safana", "Sandamu", "Zango"],
    "Kebbi": ["Aleiro", "Arewa Dandi", "Argungu", "Augie", "Bagudo", "Birnin Kebbi", "Bunza", "Dandi", "Fakai", "Gwandu", "Jega", "Kalgo", "Koko/Besse", "Maiyama", "Ngaski", "Sakaba", "Shanga", "Suru", "Wasagu/Danko", "Yauri", "Zuru"],
    "Kogi": ["Adavi", "Ajaokuta", "Ankpa", "Bassa", "Dekina", "Ibaji", "Idah", "Igalamela Odolu", "Ijumu", "Kabba/Bunu", "Kogi", "Lokoja", "Mopa-Muro", "Ofu", "Ogori/Magongo", "Okehi", "Okene", "Olamaboro", "Omala", "Yagba East", "Yagba West"],
    "Kwara": ["Asa", "Baruten", "Edu", "Ekiti", "Ifelodun", "Ilorin East", "Ilorin South", "Ilorin West", "Irepodun", "Isin", "Kaiama", "Moro", "Offa", "Oke Ero", "Oyun", "Pategi"],
    "Lagos": ["Agege", "Ajeromi-Ifelodun", "Alimosho", "Amuwo-Odofin", "Apapa", "Badagry", "Epe", "Eti Osa", "Ibeju-Lekki", "Ifako-Ijaiye", "Ikeja", "Ikorodu", "Kosofe", "Lagos Island", "Lagos Mainland", "Mushin", "Ojo", "Oshodi-Isolo", "Shomolu", "Surulere"],
    "Nasarawa": ["Akwanga", "Awe", "Doma", "Karu", "Keana", "Keffi", "Kokona", "Lafia", "Nasarawa", "Nasarawa Egon", "Obi", "Toto", "Wamba"],
    "Niger": ["Agaie", "Agwara", "Bida", "Borgu", "Bosso", "Chanchaga", "Edati", "Gbako", "Gurara", "Katcha", "Kontagora", "Lapai", "Lavun", "Magama", "Mariga", "Mashegu", "Mokwa", "Munya", "Paikoro", "Rafi", "Rijau", "Shiroro", "Suleja", "Tafa", "Wushishi"],
    "Ogun": ["Abeokuta North", "Abeokuta South", "Ado-Odo/Ota", "Egbado North", "Egbado South", "Ewekoro", "Ifo", "Ijebu East", "Ijebu North", "Ijebu North East", "Ijebu Ode", "Ikenne", "Imeko Afon", "Ipokia", "Obafemi Owode", "Odeda", "Odogbolu", "Ogun Waterside", "Remo North", "Shagamu"],
    "Ondo": ["Akoko North-East", "Akoko North-West", "Akoko South-East", "Akoko South-West", "Akure North", "Akure South", "Ese Odo", "Idanre", "Ifedore", "Ilaje", "Ile Oluji/Okeigbo", "Irele", "Odigbo", "Okitipupa", "Ondo East", "Ondo West", "Ose", "Owo"],
    "Osun": ["Aiyedaade", "Aiyedire", "Atakumosa East", "Atakumosa West", "Boluwaduro", "Boripe", "Ede North", "Ede South", "Egbedore", "Ejigbo", "Ife Central", "Ife East", "Ife North", "Ife South", "Ifedayo", "Ifelodun", "Ila", "Ilesa East", "Ilesa West", "Irepodun", "Irewole", "Isokan", "Iwo", "Obokun", "Odo Otin", "Ola Oluwa", "Olorunda", "Oriade", "Orolu", "Osogbo"],
    "Oyo": ["Afijio", "Akinyele", "Atiba", "Atisbo", "Egbeda", "Ibadan North", "Ibadan North-East", "Ibadan North-West", "Ibadan South-East", "Ibadan South-West", "Ibarapa Central", "Ibarapa East", "Ibarapa North", "Ido", "Irepo", "Iseyin", "Itesiwaju", "Iwajowa", "Kajola", "Lagelu", "Ogbomosho North", "Ogbomosho South", "Ogo Oluwa", "Olorunsogo", "Oluyole", "Ona Ara", "Orelope", "Ori Ire", "Oyo East", "Oyo West", "Saki East", "Saki West", "Surulere"],
    "Plateau": ["Barkin Ladi", "Bassa", "Bokkos", "Jos East", "Jos North", "Jos South", "Kanam", "Kanke", "Langtang North", "Langtang South", "Mangu", "Mikang", "Pankshin", "Qua'an Pan", "Riyom", "Shendam", "Wase"],
    "Rivers": ["Abua/Odual", "Ahoada East", "Ahoada West", "Akuku-Toru", "Andoni", "Asari-Toru", "Bonny", "Degema", "Eleme", "Emohua", "Etche", "Gokana", "Ikwerre", "Khana", "Obio/Akpor", "Ogba/Egbema/Ndoni", "Ogu/Bolo", "Okrika", "Omuma", "Opobo/Nkoro", "Oyigbo", "Port Harcourt", "Tai"],
    "Sokoto": ["Binji", "Bodinga", "Dange Shuni", "Gada", "Goronyo", "Gudu", "Gwadabawa", "Illela", "Isa", "Kebbe", "Kware", "Rabah", "Sabon Birni", "Shagari", "Silame", "Sokoto North", "Sokoto South", "Tambuwal", "Tangaza", "Tureta", "Wamako", "Wurno", "Yabo"],
    "Taraba": ["Ardo Kola", "Bali", "Donga", "Gashaka", "Gassol", "Ibi", "Jalingo", "Karim Lamido", "Kumi", "Lau", "Sardauna", "Takum", "Ussa", "Wukari", "Yorro", "Zing"],
    "Yobe": ["Bade", "Bursari", "Damaturu", "Fika", "Fune", "Geidam", "Gujba", "Gulani", "Jakusko", "Karasuwa", "Machina", "Nangere", "Nguru", "Potiskum", "Tarmuwa", "Yunusari", "Yusufari"],
    "Zamfara": ["Anka", "Bakura", "Birnin Magaji/Kiyaw", "Bukkuyum", "Bungudu", "Gummi", "Gusau", "Kaura Namoda", "Maradun", "Maru", "Shinkafi", "Talata Mafara", "Chafe", "Zurmi"]
};

// State change event handler
document.getElementById('state').addEventListener('change', function() {
    const state = this.value;
    const lgaSelect = document.getElementById('lga');
    
    // Clear previous options
    lgaSelect.innerHTML = '';
    
    // Add default option
    const defaultOption = document.createElement('option');
    defaultOption.selected = true;
    defaultOption.disabled = true;
    defaultOption.value = "";
    defaultOption.textContent = "Choose LGA";
    lgaSelect.appendChild(defaultOption);
    
    // Enable LGA select
    lgaSelect.disabled = false;
    
    // Add LGAs for selected state
    if (state && lgas[state]) {
        lgas[state].forEach(function(lga) {
            const option = document.createElement('option');
            option.value = lga;
            option.textContent = lga;
            lgaSelect.appendChild(option);
        });
    }
});

// Service field description display
document.getElementById('service_field').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const description = selectedOption.getAttribute('data-description');
    document.getElementById('field-description').textContent = description || '';
});

</script>