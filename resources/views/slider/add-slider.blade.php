<x-app-layout>
         <h1 class="font-bold text-3xl my-3">Add Slider</h1>
                <form class="w-96 mx-auto bg-white rounded shadow px-4 py-2" action="{{ route('store.slider') }}" method="POST" enctype="multipart/form-data">   
                    @csrf
                    <div class="my-3">
                        <label class="font-semibold text-base text-gray-900 dark:text-gray-900">Slider Title</label>
                        <input type="text" name="title" class="rounded w-full text-black dark:text-gray-900" placeholder="Slider Title">
                    </div>
                    
                    <div class="my-3">
                        <label class="font-semibold text-base text-gray-900 dark:text-gray-900">Slider Description</label>
                        <textarea class="rounded w-full text-black dark:text-gray-900" id="exampleFormControlTextarea1" rows="3" name="description"></textarea>
                    </div>
                    <div class="my-3">
                        <label class="font-semibold text-base text-gray-900 dark:text-gray-900">Slider Image</label>
                        <input type="file" name="image" class="form-control-file">
                    </div>
                    <div class="flex justify-evenly my-3 text-gray-800">
                        <button type="submit" class="rounded bg-green-500 px-4 py-2 text-white hover:bg-green-600">Submit</button>
                    </div>
                </form>
        </div>
</x-app-layout>