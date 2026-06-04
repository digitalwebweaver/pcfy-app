@extends('admin.layouts.app')
@section('title', 'Settings')

@php
  $s   = fn(string $key, string $default = '') => $settings[$key]?->value ?? $default;
  $tab = session('tab', request('tab', 'brand'));
@endphp

@push('styles')
<style>
.settings-tabs { display:flex; gap:2px; border-bottom:2px solid var(--border); margin-bottom:24px; flex-wrap:wrap; }
.settings-tab  { padding:8px 16px; font-size:13px; font-weight:600; color:var(--muted); cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; text-decoration:none; white-space:nowrap; }
.settings-tab.active { color:var(--navy); border-bottom-color:var(--navy); }
.settings-tab:hover:not(.active) { color:var(--navy); }
.settings-pane { display:none; }
.settings-pane.active { display:block; }
.field-hint { font-size:11.5px; color:var(--muted); margin-top:3px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div><div class="page-title">Settings</div><div class="page-sub">Global site configuration</div></div>
</div>

<div class="page-body">

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

{{-- Tab nav --}}
<div class="settings-tabs">
  @foreach(['brand'=>'Brand','contact'=>'Contact','social'=>'Social','seo'=>'SEO','smtp'=>'Email / SMTP','analytics'=>'Analytics','footer'=>'Footer','announcement'=>'Announcement'] as $t=>$label)
    <a href="#" class="settings-tab {{ $tab===$t?'active':'' }}" data-tab="{{ $t }}">{{ $label }}</a>
  @endforeach
</div>

{{-- BRAND --}}
<div class="settings-pane {{ $tab==='brand'?'active':'' }}" id="pane-brand">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="brand">
  <div style="max-width:600px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">Brand identity</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="field"><label>Site name <span class="req">*</span></label>
          <input class="input" type="text" name="site_name" value="{{ $s('site_name','pcfy.in') }}" required>
        </div>
        <div class="field"><label>Tagline</label>
          <input class="input" type="text" name="site_tagline" value="{{ $s('site_tagline') }}" placeholder="Better computers. Half the price.">
        </div>
        <div class="field"><label>Accent colour</label>
          <div style="display:flex;gap:8px;align-items:center;">
            <input type="color" name="accent_color" value="{{ $s('accent_color','#f59e0b') }}" style="width:44px;height:36px;border:1.5px solid var(--border);border-radius:6px;cursor:pointer;">
            <input class="input" type="text" id="accent-hex" value="{{ $s('accent_color','#f59e0b') }}" style="width:100px;" readonly>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save brand settings</button>
      </div>
    </div>
  </div>
</form>
</div>

{{-- CONTACT --}}
<div class="settings-pane {{ $tab==='contact'?'active':'' }}" id="pane-contact">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="contact">
  <div style="max-width:600px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">Contact details</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="field"><label>Phone</label>
          <input class="input" type="text" name="contact_phone" value="{{ $s('contact_phone') }}" placeholder="+91 98765 43210">
        </div>
        <div class="field"><label>Email</label>
          <input class="input" type="email" name="contact_email" value="{{ $s('contact_email') }}" placeholder="hello@pcfy.in">
        </div>
        <div class="field"><label>Address</label>
          <textarea class="input" name="contact_address" rows="2" style="resize:vertical;">{{ $s('contact_address') }}</textarea>
        </div>
        <div class="field"><label>WhatsApp number <span style="font-weight:400;color:var(--muted);">(digits only, with country code)</span></label>
          <input class="input" type="text" name="whatsapp_number" value="{{ $s('whatsapp_number') }}" placeholder="919876543210">
          <div class="field-hint">Used for wa.me links on quotes and contact page.</div>
        </div>
        <div class="field"><label>WhatsApp default message</label>
          <input class="input" type="text" name="whatsapp_message" value="{{ $s('whatsapp_message') }}" placeholder="Hi, I have a query about pcfy.in">
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save contact settings</button>
      </div>
    </div>
  </div>
</form>
</div>

{{-- SOCIAL --}}
<div class="settings-pane {{ $tab==='social'?'active':'' }}" id="pane-social">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="social">
  <div style="max-width:600px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">Social media links</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        @foreach(['social_instagram'=>'Instagram','social_linkedin'=>'LinkedIn','social_facebook'=>'Facebook','social_twitter'=>'Twitter / X','social_youtube'=>'YouTube'] as $key=>$label)
        <div class="field"><label>{{ $label }}</label>
          <input class="input" type="text" name="{{ $key }}" value="{{ $s($key) }}" placeholder="https://…">
        </div>
        @endforeach
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save social links</button>
      </div>
    </div>
  </div>
</form>
</div>

{{-- SEO --}}
<div class="settings-pane {{ $tab==='seo'?'active':'' }}" id="pane-seo">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="seo">
  <div style="max-width:700px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">SEO defaults</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="field"><label>Default meta title <span style="font-weight:400;color:var(--muted);">(max 160 chars)</span></label>
          <input class="input" type="text" name="seo_title" value="{{ $s('seo_title') }}" maxlength="160">
        </div>
        <div class="field"><label>Default meta description <span style="font-weight:400;color:var(--muted);">(max 320 chars)</span></label>
          <textarea class="input" name="seo_description" rows="3" maxlength="320" style="resize:vertical;">{{ $s('seo_description') }}</textarea>
        </div>
        <div class="field"><label>Default OG image URL</label>
          <input class="input" type="text" name="seo_og_image" value="{{ $s('seo_og_image') }}" placeholder="https://pcfy.in/og-image.jpg">
          <div class="field-hint">Used as fallback for social sharing cards.</div>
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save SEO defaults</button>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><div class="card-title">robots.txt</div></div>
      <div class="card-body">
        <div class="field">
          <textarea class="input" name="robots_txt" rows="8" style="resize:vertical;font-family:monospace;font-size:12.5px;">{{ $s('robots_txt') }}</textarea>
          <div class="field-hint">Served at <code>/robots.txt</code> · <a href="/robots.txt" target="_blank" style="color:var(--navy);">Preview →</a></div>
        </div>
        <div style="margin-top:10px;display:flex;gap:8px;align-items:center;">
          <a href="{{ route('sitemap') }}" target="_blank" class="btn btn-ghost" style="font-size:12px;">View sitemap.xml →</a>
        </div>
      </div>
    </div>
  </div>
</form>
</div>

{{-- SMTP --}}
<div class="settings-pane {{ $tab==='smtp'?'active':'' }}" id="pane-smtp">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="smtp">
  <div style="max-width:600px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">Email / SMTP</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="field"><label>Mail driver</label>
          <select class="select" name="mail_driver" style="width:100%;">
            @foreach(['log'=>'Log (dev only)','smtp'=>'SMTP','sendmail'=>'Sendmail'] as $v=>$l)
              <option value="{{ $v }}" {{ $s('mail_driver','log')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
          <div class="field-hint">"Log" writes emails to laravel.log — switch to SMTP for production.</div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 120px;gap:10px;">
          <div class="field"><label>SMTP host</label>
            <input class="input" type="text" name="mail_host" value="{{ $s('mail_host') }}" placeholder="smtp.mailtrap.io">
          </div>
          <div class="field"><label>Port</label>
            <input class="input" type="text" name="mail_port" value="{{ $s('mail_port','587') }}">
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="field"><label>Username</label>
            <input class="input" type="text" name="mail_username" value="{{ $s('mail_username') }}">
          </div>
          <div class="field"><label>Password</label>
            <input class="input" type="password" name="mail_password" value="{{ $s('mail_password') }}" autocomplete="new-password">
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="field"><label>From name</label>
            <input class="input" type="text" name="mail_from_name" value="{{ $s('mail_from_name','pcfy.in') }}">
          </div>
          <div class="field"><label>From email</label>
            <input class="input" type="email" name="mail_from_email" value="{{ $s('mail_from_email') }}">
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save email settings</button>
      </div>
    </div>
  </div>
</form>
</div>

{{-- ANALYTICS --}}
<div class="settings-pane {{ $tab==='analytics'?'active':'' }}" id="pane-analytics">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="analytics">
  <div style="max-width:500px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">Analytics</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="field"><label>Google Analytics ID</label>
          <input class="input" type="text" name="ga_id" value="{{ $s('ga_id') }}" placeholder="G-XXXXXXXXXX">
          <div class="field-hint">Leave blank to disable. Added to all frontend pages.</div>
        </div>
        <div class="field"><label>Google Tag Manager ID</label>
          <input class="input" type="text" name="gtm_id" value="{{ $s('gtm_id') }}" placeholder="GTM-XXXXXXX">
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save analytics</button>
      </div>
    </div>
  </div>
</form>
</div>

{{-- FOOTER --}}
<div class="settings-pane {{ $tab==='footer'?'active':'' }}" id="pane-footer">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="footer">
  <div style="max-width:600px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">Footer settings</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="field"><label>Copyright text</label>
          <input class="input" type="text" name="footer_copyright" value="{{ $s('footer_copyright') }}" placeholder="© 2026 pcfy.in">
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          @foreach(['footer_col1_title'=>'Col 1 heading','footer_col2_title'=>'Col 2 heading','footer_col3_title'=>'Col 3 heading'] as $key=>$label)
          <div class="field"><label>{{ $label }}</label>
            <input class="input" type="text" name="{{ $key }}" value="{{ $s($key) }}">
          </div>
          @endforeach
        </div>
        <div class="field-hint">Footer column links are managed via <a href="{{ route('adminhu.menus.index') }}" style="color:var(--navy);">Menu Manager →</a></div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save footer settings</button>
      </div>
    </div>
  </div>
</form>
</div>

{{-- ANNOUNCEMENT --}}
<div class="settings-pane {{ $tab==='announcement'?'active':'' }}" id="pane-announcement">
<form method="POST" action="{{ route('adminhu.settings.update') }}">
  @csrf @method('PUT')
  <input type="hidden" name="tab" value="announcement">
  <div style="max-width:600px;display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title">Announcement bar</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
          <input type="hidden" name="announcement_enabled" value="0">
          <input type="checkbox" name="announcement_enabled" value="1" style="width:16px;height:16px;"
                 {{ $s('announcement_enabled','1')==='1' ? 'checked' : '' }}>
          Show announcement bar on frontend
        </label>
        <div class="field">
          <label>Announcement items <span style="font-weight:400;color:var(--muted);">(one per line)</span></label>
          @php
            $items = json_decode($s('announcement_items','[]'), true) ?? [];
          @endphp
          <textarea class="input" name="announcement_items_text" rows="6" style="resize:vertical;font-size:13px;">{{ implode("\n", $items) }}</textarea>
          <div class="field-hint">Emojis welcome. These scroll in the topbar.</div>
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save announcement settings</button>
      </div>
    </div>
  </div>
</form>
</div>

</div>
@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.settings-tab').forEach(tab => {
  tab.addEventListener('click', e => {
    e.preventDefault();
    const t = tab.dataset.tab;
    document.querySelectorAll('.settings-tab').forEach(x => x.classList.toggle('active', x.dataset.tab === t));
    document.querySelectorAll('.settings-pane').forEach(x => x.classList.toggle('active', x.id === 'pane-'+t));
  });
});
// Colour picker sync
const picker = document.querySelector('input[type=color][name=accent_color]');
const hex    = document.getElementById('accent-hex');
if (picker && hex) picker.addEventListener('input', () => hex.value = picker.value);
</script>
@endpush
