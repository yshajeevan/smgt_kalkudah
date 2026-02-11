<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 2px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.container-table{
    padding:22px;
}
</style>
<div class="container-table">
    <h4>Today Pending Attendance ({{$attendances->count()}})</h4>
    <table>
      <tr>
        <th>Name of schools</th>
      </tr>
      @foreach($attendances as $attendance)
      <tr>
        <td><a href="{!! route('attendance.createlink', ['instid'=>$attendance->id]) !!}">{{$attendance->institute}}</a></td>
      </tr>
      @endforeach
    </table>
</div>
