<div style="display: flex; gap: 10px;">
    @foreach(json_decode($getState(), true) as $image)
        <img src="{{ Storage::disk('public')->url($image) }}" alt="Image" style="width: 50px; height: 50px; object-fit: cover;">
    @endforeach
</div>