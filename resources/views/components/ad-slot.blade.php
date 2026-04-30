@if(config('lista.ads.enabled'))
    <div class="my-4 text-center">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ $client }}"
             data-ad-slot="{{ $slotId }}"
             data-ad-format="{{ $format }}"
             data-full-width-responsive="true"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
@else
    <!-- ads placeholder: {{ $position }} -->
@endif
