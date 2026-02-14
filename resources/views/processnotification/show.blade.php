<div id="messages">
    <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-cogs fa-fw"></i>
        <!-- Counter - Messages -->
        @if(count(Helper::pendingprocess())>10)
            <span data-count="10" class="badge badge-danger badge-counter">10+</span>
        @else 
            <span data-count="{{count(Helper::pendingprocess())}}" class="badge badge-danger badge-counter">{{count(Helper::pendingprocess())}}</span>
        @endif
    </a>
    <!-- Dropdown - Messages -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
        <h6 class="dropdown-header">
        Pending Process
        </h6>
        <div id="message-items">
            @foreach(Helper::pendingprocess() as $process)
                <a class="dropdown-item d-flex align-items-center" href="{{route('process.edit',$process->id)}}">
                    <div class="dropdown-list-image mr-3">
                        @if($process->employee?->photo)
                        <img class="rounded-circle" src="{{$process->employee->photo}}" alt="profile">
                        @else 
                        <img class="rounded-circle" src="{{asset('backend/img/avatar.png')}}" alt="default img">
                        @endif
                        {{-- <div class="status-indicator bg-success"></div> --}}
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate">{{$process->employee?->namewithinitial ?? 'No user found'}}</div>
                        <div class="small text-gray-500">{{$process->service->service}} | Process ID: {{$process->id}}</div>
                    </div>
                </a>
                @if($loop->index+1==10) 
                  @php 
                    break;
                  @endphp
                @endif
            @endforeach
        </div>
        <a class="dropdown-item text-center small text-gray-500" href="{{ route('process.index',1) }}">Read More Pending Processes</a>
    </div>
</div>


