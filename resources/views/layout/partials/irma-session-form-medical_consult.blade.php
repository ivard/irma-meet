<!-- /resources/views/layout/irma-session-form.blade.php -->
<script>
    function clearError(element) {
        var el = document.getElementById("error");
        el.style.display = "none";
        element.className = element.className.replace(/\bis-invalid\b/g, "");
    }
</script>


        <form method="post" class="" action="{{ route('irma_session.store') }}">
            {{ csrf_field() }}
            <input id="meeting_type" name="meeting_type"  value="medical_consult" type="hidden">
        <div class="form-group">
            <label class="control-label"  for="meeting_name">{{ __('Meeting name') }}</label>
                <input id="meeting_name" name="meeting_name"  value="{{ old('meeting_name') }}" type="text" class="form-control @error('meeting_name') is-invalid @enderror" onfocus="clearError(this)">
        </div>

        <div class="form-group">
            <label class="control-label"  for="hoster_name">{{ __('Host name') }}</label>
                <input id="hoster_name" name="hoster_name"  value="{{ $validated_name }}" type="text" class="form-control @error('hoster_name') is-invalid @enderror" readonly>
        </div>

        <div class="form-group">
            <label class="control-label"  for="hoster_email_address">{{ __('Host email address') }}</label>
                <input id="hoster_email_address"  value="{{ $validated_email }}" name="hoster_email_address" type="text" class="form-control @error('hoster_email_address') is-invalid @enderror" readonly>
        </div>

        <div class="form-group">
            <label for="invitation_note">{{ __('Invitation note') }}</label>
            <textarea id="invitation_note" name="invitation_note" class="form-control @error('invitation_note') is-invalid @enderror" onfocus="clearError(this)">{{ old('invitation_note') }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="participant_email_address1">{{ __('Participant email address') }}</label>
            <input id="participant_email_address1"  value="{{ old('participant_email_address1') }}" name="participant_email_address1" type="text" class="form-control @error('participant_email_address1') is-invalid @enderror" onfocus="clearError(this)">
        </div>
        <div class="form-group">
            <input type="checkbox" name="agreed" id="agreed" value="true" class="@error('agreed') is-invalid @enderror">
            <label for="agreed"><span>{{ __("I hereby explicitly request ProcoliX to securely process the personal data of the patient that I invite, with certainty about his/her identity in the video meeting that I'm now setting up as goal, and to remove these data immediately afterwards.") }}</span></label>
        </div>
        
        <div class="form-group">        
            <button type="submit" class="btn btn-primary">{{ __('Send invite') }}</button>
        </div>


        </form>


@if ($errors->any())
    <div id="error" class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Create Post Form -->