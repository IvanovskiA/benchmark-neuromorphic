@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" />
@endif

@if (session('error'))
    <x-ui.alert type="error" :message="session('error')" />
@endif

@if (isset($errors) && $errors->any())
    <x-ui.alert type="error" :message="$errors->first()" />
@endif
