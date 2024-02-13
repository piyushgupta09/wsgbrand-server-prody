<ul 
    id="{{ $tabId }}" role="tablist"
    class="navtabs nav nav-pills nav-justified border">
    @foreach ($tabs as $tab)
        <li class="nav-item {{ $loop->first ? '' : 'border-start' }}">
            <a class="nav-link fw-bold {{ $tab['slug'] == $currentTab ? 'active' : '' }}"
                href="{{ route('products.show', ['product' => $model->slug, 'tab' => $tab['slug']]) }}">
                {{ $tab['name'] }}
            </a>
        </li>
    @endforeach
</ul>

<style scoped>
    .navtabs.nav-pills .nav-item .nav-link {
        border-radius: 0 !important;
    }

    .navtabs.nav-pills .nav-link {
        background-color: #212529;
        color: #fff;
    }

    .navtabs.nav-pills .nav-link.active {
        color: #fff;
        background-color: #e13848;
    }

    .navtabs.nav-pills .nav-link:hover {
        color: #e13848;
        background-color: #212529;
    }
</style>

{{-- <style scoped>
    .navtabs.nav-pills .nav-item .nav-link {
        border-radius: 0 !important;
    }

    .navtabs.nav-pills .nav-link {
        background-color: #f8f9fa;
        color: #343434;
    }

    .navtabs.nav-pills .nav-link.active {
        color: #fff;
        background-color: #212529;
    }

    .navtabs.nav-pills .nav-link:hover {
        color: #fff;
        background-color: #212529;
    }
</style> --}}