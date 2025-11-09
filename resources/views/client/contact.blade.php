@extends('layouts.client')
@section('title', 'Liên hệ')

@section('content')
<section class="container mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-blue-700 mb-8 text-center">Liên hệ</h2>

    <div class="max-w-2xl mx-auto bg-white shadow-md rounded-xl p-8">
        <form>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Họ và tên</label>
                <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nội dung</label>
                <textarea rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <button type="submit" class="btn-primary w-full">Gửi liên hệ</button>
        </form>
    </div>
</section>
@endsection
