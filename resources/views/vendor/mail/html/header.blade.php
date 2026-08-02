@props(['url'])
<tr>
<td class="header" style="padding: 25px 0; text-align: center;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel' || empty(trim($slot)))
<img src="{{ config('app.url') }}/images/logo.png" class="logo" alt="EasyImmob" style="height: 56px; max-height: 56px; width: auto; vertical-align: middle;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
