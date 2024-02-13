<ul 
    id="{{ $sectionId }}" role="tablist"
    class="sectabs nav flex-column nav-pills border"
    style="width: fit-content;">
    @foreach ($sections as $section)
        <li class="nav-item" style="width: 150px;">
            <a class="nav-link fw-bold {{ $section['slug'] == $currentSection ? 'active' : '' }}"
                href="{{ route('products.show', [
                    'product' => $model->slug, 
                    'tab' => $currentTab,
                    'section' => $section['slug'],
                ]) }}">
                {{ chr(96 + $loop->iteration) }}. {{ $section['name'] }}
            </a>
        </li>
    @endforeach
</ul>

<style scoped>
    .sectabs.nav-pills .nav-item .nav-link {
        border-radius: 0 !important;
    }

    .sectabs.nav-pills .nav-link {
        background-color: #f8f9fa;
        color: #212529;
    }

    .sectabs.nav-pills .nav-link.active {
        color: #e13848;
        background-color: #e1e1e1;
    }

    .sectabs.nav-pills .nav-link:hover {
        color: #e13848;
        background-color: #e1e1e1;
    }
</style>