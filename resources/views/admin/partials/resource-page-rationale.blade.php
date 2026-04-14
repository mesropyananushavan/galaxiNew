<section class="card">
    <h3 style="margin: 0; font-size: 1.1rem;">Why this page exists now</h3>
    <ul class="list">
        @foreach ($items as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
</section>
