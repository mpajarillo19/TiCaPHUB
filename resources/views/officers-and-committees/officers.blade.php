<x-app-layout>
    <x-page-title>{{ $title }}</x-page-title>
    <div>
        <div class="text-center">
            <h1 class="font-bold text-4xl mb-3">{{ $ticap->name }}</h1>
            <h1 class="font-semibold text-3xl">Election Results</h1>
        </div>
        {{-- APPOINT COMMITTEE HEADS --}}
        @can('appoint committee head')
        <a href="/officers-and-committees/appoint" class="inline-block bg-blue-500 hover:bg-blue-600 rounded text-white px-2 py-1">Appoint Committee Heads</a>
        @endcan
        <div class="flex flex-wrap justify-evenly w-full">
            @foreach(\App\Models\School::all() as $school)
                @if($school->is_involved)
                <div class="w-2/5">
                    <h1 class="text-2xl font-semibold">{{ $school->name }}</h1>
                    @foreach(\App\Models\Specialization::all() as $specialization)
                    <div>
                        <div class="mb-3">
                            <h1 class="text-xl">{{ $specialization->name }}</h1>
                        </div>
                        <table class="w-full rounded-lg shadow-lg mx-1 text-center my-3">
                            <thead>
                                <tr class="bg-gray-100 uppercase border-b border-gray-600">
                                    <th class="px-4 py-3">Position</th>
                                    <th class="px-4 py-3">Officer</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach(\App\Models\Position::all() as $position)
                                <tr>  
                                    <td class="border">{{ $position->name }}</td>
                                    <td class="border">
                                        {{-- CHECK USER HAS VOTED AND ELECTION FINISHED --}}
                                        @if(!$ticap->election_finished && !$user->userSpecialization->has_voted)
                                            none
                                        @elseif(!$ticap->election_finished && $user->userSpecialization->has_voted)
                                            wait for results
                                        @else
                                            @foreach($officers as $officer)
                                                @if(
                                                    $officer->user->candidate->specialization->id == $specialization->id &&
                                                    $officer->user->candidate->position->id == $position->id
                                                )
                                                    {{ $officer->user->first_name . ' ' . $officer->user->middle_name . ' ' . $officer->user->last_name . ' ' }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>