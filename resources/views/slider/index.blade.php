<x-app-layout>
    <h1 class="font-bold text-3xl my-3">Home Slider</h1>
    <div class="flex justify-between my-4">
    <a href="{{ route('add.slider') }}" class="inline-block md:w-32 bg-green-600 dark:bg-green-100 text-white dark:text-white-800 font-bold py-3 px-6 rounded-lg mt-4 hover:bg-green-500 dark:hover:bg-green-200 transition ease-in-out duration-300">+ Sliders</a>
    </div>

    <table class="table-auto w-full">
        <thead>
        <tr class="text-center text-md font-semibold tracking-wide text-gray-900 bg-gray-100 uppercase border-b border-gray-600">
            <th class="px-4 py-3">SL</th>
            <th class="px-4 py-3">Slider Title</th>
            <th class="px-4 py-3">Description</th>
            <th class="px-4 py-3">Image</th>
            <th class="px-4 py-3">Action</th>
        </tr>
        </thead>
        <tbody class="w-auto bg-white text-center">
            @php($i = 1)
        @foreach($sliders as $slider)
        <tr class="text-gray-700">
            <th>{{ $i++ }}</th>
            <td>{{ $slider->title }}</td>
            <td>{{ $slider->description }}</td>
            <td> <img src="{{ asset($slider->image) }}" style="height:40px; width:70px;" alt=""></td>
            <td>
                {{-- <a href="{{ url('slider/edit'.$slider->id) }}" class="w-24 rounded shadow px-2 py-1 my-0.5 text-white bg-blue-500 hover:bg-blue-600">Edit</a> --}}
                <a href="{{ url('slider/delete'.$slider->id) }}" onclick="return confirm('Are you sure to delete')" class="w-24 rounded shadow px-2 py-1 my-0.5 text-white bg-red-500 hover:bg-red-600">Delete</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</x-app-layout>