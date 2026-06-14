<div id="captcha-widget" class="captcha-group">
    <label for="captcha">Verifikasi CAPTCHA</label>
    <div class="captcha-row">
        <div class="captcha-box">{{ $captchaQuestion ?? session('captcha_data.question') }}</div>
        <button type="button" class="btn-refresh" id="refresh-captcha">Segarkan</button>
    </div>

    <input type="text" name="captcha" id="captcha" value="{{ old('captcha') }}" placeholder="Jawaban CAPTCHA" autocomplete="off" required>

    @error('captcha')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>
