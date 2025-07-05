@extends('admin.layouts.admin')

@section('title', 'Live Chat')

@section('content')
<div class="pb-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Live Chat</h1>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <livewire:chat.admin-chat />
    </div>
</div>
@endsection 