@forelse($students as $stu)
<tr data-id="{{ $stu->id }}">
    <td>{{ $loop->iteration }}</td>

    <td><input type="text" class="form-control form-control-sm quick-input name" value="{{ $stu->name }}"></td>

    <td><input type="text" class="form-control form-control-sm quick-input admission_number" value="{{ $stu->admission_number }}"></td>

    {{-- Inline grade select: use $gradesForRows supplied by controller --}}
    <td>
        <select class="form-select form-select-sm grade-select">
            <option value="">--</option>
            @foreach($gradesForRows as $g)
                <option value="{{ $g->id }}" {{ $stu->grade_id == $g->id ? 'selected' : '' }}>
                    {{ $g->name }}
                </option>
            @endforeach
        </select>
    </td>

    <td>
        <div class="d-flex align-items-center">
            <div class="ews-swatch me-2" style="width:28px;height:18px;border:1px solid #ccc;background:{{ $stu->ews_color==1? '#00ff00' : ($stu->ews_color==2? '#ffa500' : ($stu->ews_color==3? '#ff0000':'#fff')) }};" data-ews="{{ $stu->ews_color }}"></div>
            <select class="form-select form-select-sm ews-select" style="width:auto;">
                <option value="">--</option>
                <option value="1" {{ $stu->ews_color==1 ? 'selected':'' }}>Green</option>
                <option value="2" {{ $stu->ews_color==2 ? 'selected':'' }}>Orange</option>
                <option value="3" {{ $stu->ews_color==3 ? 'selected':'' }}>Red</option>
            </select>
        </div>
    </td>

    <td>
        <select class="form-select form-select-sm status">
            <option value="Active" {{ $stu->status=='Active'?'selected':'' }}>Active</option>
            <option value="Droped out" {{ $stu->status=='Droped out'?'selected':'' }}>Droped out</option>
        </select>
    </td>

    <td>{{ optional($stu->institute)->name ?? $stu->institute_id }}</td>

    <td>
        <button class="btn btn-sm btn-success btn-quick-save">Update</button>
        <a href="{{ route('students.edit', $stu) }}" class="btn btn-sm btn-primary">Edit</a>
        @if(Auth::user()->role === 'super_admin')
            <form method="POST" action="{{ route('students.destroy', $stu) }}" style="display:inline-block" onsubmit="return confirm('Delete?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
            </form>
        @endif
    </td>
</tr>
            @empty
                    <tr><td colspan="8" class="text-center">No records</td></tr>
                @endforelse
