@extends('layouts.master')

@section('main-content')
@php
    $groupedSubjects = collect($subjects)->groupBy('subject_category');

    // Function to convert to sentence case and remove underscores
    function formatCategory($category) {
        // Replace underscores with spaces and convert to lowercase
        $category = str_replace('_', ' ', $category);
        // Convert to sentence case (capitalize the first letter)
        return ucfirst(strtolower($category));
    }

    // List of relevant categories to include in the total calculation
    $relevantCategories = [
        '13_years_education', 
        'advanced_level', 
        'others', 
        'primary', 
        'secondary', 
        'secondary_b1', 
        'secondary_b2', 
        'secondary_b3', 
        'secondary_bi',
    ];

    // Initialize totals
    $totalApproved = 0;
    $totalAvailable = 0;
    $schoolAdminApproved = 0;
    $schoolAdminAvailable = 0;
    $peformingPrincipalApproved = 0;
    $peformingPrincipalAvailable = 0;
    $developmentOfficerApproved = 0;
    $developmentOfficerAvailable = 0;

    // Calculate totals based on relevant categories
    foreach ($subjects as $subject) {
        if (in_array($subject['subject_category'], $relevantCategories)) {
            $totalApproved += $subject['approved'];
            $totalAvailable += $subject['available'];
        }
        if ($subject['subject_category'] === 'school_administration') {
            $schoolAdminApproved += $subject['approved'];
            $schoolAdminAvailable += $subject['available'];
        }
    }

    // Calculate surplus or deficit SLTS
    $surplus = $totalAvailable - $totalApproved;

    // Calculate surplus or deficit SLPS
    $schoolAdminSurplus = $schoolAdminAvailable - $schoolAdminApproved;

    // Calculate surplus or deficit Performing Principals
    $peformingPrincipalApproved += 0;
    $peformingPrincipalAvailable += $performing_principals;
    $performingPrincipalSurplus = $peformingPrincipalAvailable - $peformingPrincipalApproved;

    // Calculate surplus or deficit Development Officers
    $developmentOfficerApproved += 0;
    $developmentOfficerAvailable += $development_officers;
    $developmentOfficerSurplus = $developmentOfficerAvailable - $developmentOfficerApproved;

@endphp
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Cadre Summary</h6>
    </div>
    <div class="card-body">
		<button type="button" id="print" class="commonButton">
			<i class="fas fa-save"></i>&nbsp;Print
		</button>
		<!-- Live Search Input -->
        <div class="form-group mt-4">
            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
        </div>
        @if(!empty($institute))
            <h1 id="instituteName" class="mb-3">
                {{ $institute->institute }}
            </h1>
        @endif
	  	<div class="table-responsive">
            <table width='100%' border='1' id='cadretbl' style='border-collapse: collapse;' class="table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Subject Category</th>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Approved Cadre</th>
                        <th>Available Cadre</th>
                        <th>Difference</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach ($groupedSubjects as $category => $subjectsInCategory)
                        @php
                            $rowCount = count($subjectsInCategory);
                            $subtotalApproved = $subjectsInCategory->sum('approved');
                            $subtotalAvailable = $subjectsInCategory->sum('available');
                            $subtotalDifference = $subjectsInCategory->sum('difference');
                            
                            // Format the category name
                            $formattedCategory = formatCategory($category);
                        @endphp
                        
                        @foreach ($subjectsInCategory as $index => $subject)
                            <tr>
                                @if ($index == 0)
                                    <td rowspan="{{ $rowCount }}">{{ $formattedCategory }}</td>
                                @endif
                                <td>{{ $subject['subject_name'] }}</td>
                                <td>{{ $subject['subject_code'] }}</td>
                                <td>{{ $subject['approved'] }}</td>
                                <td>{{ $subject['available'] }}</td>
                                <td>{{ $subject['difference'] }}</td>
                            </tr>
                        @endforeach

                        <!-- Subtotal row -->
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Subtotal for {{ $formattedCategory }}</strong></td>
                            <td><strong>{{ $subtotalApproved }}</strong></td>
                            <td><strong>{{ $subtotalAvailable }}</strong></td>
                            <td><strong>{{ $subtotalDifference }}</strong></td>
                        </tr>
                    @endforeach

                    <!-- Total row for specific categories -->
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total of SLTS</strong></td>
                        <td><strong>{{ $totalApproved }}</strong></td>
                        <td><strong>{{ $totalAvailable }}</strong></td>
                        <td><strong>{{ $surplus }}</strong></td>
                    </tr>
                    @if (!$isPerform)
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total of Performing Principals</strong></td>
                        <td><strong>{{ $peformingPrincipalApproved }}</strong></td>
                        <td><strong>{{ $peformingPrincipalAvailable }}</strong></td>
                        <td><strong>{{ $performingPrincipalSurplus }}</strong></td>
                    </tr>
                    @endif
                    @if (!$isDevOfficer)
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total of Development Officers (Teaching)</strong></td>
                        <td><strong>{{ $developmentOfficerApproved }}</strong></td>
                        <td><strong>{{ $developmentOfficerAvailable }}</strong></td>
                        <td><strong>{{ $developmentOfficerSurplus }}</strong></td>
                    </tr>
                    @endif
                    <!-- Subtotal row for school administration -->
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Subtotal of SLPS</strong></td>
                        <td><strong>{{ $schoolAdminApproved }}</strong></td>
                        <td><strong>{{ $schoolAdminAvailable }}</strong></td>
                        <td><strong>{{ $schoolAdminSurplus }}</strong></td>
                    </tr>
                </tbody>
            </table>
		</div>
	</div>
</div>
@endsection

@push('styles')
<style>

</style>
@endpush

@push('scripts')
<script>
	function printData() {
        var divToPrint = document.getElementById("cadretbl");
        var instituteName = document.getElementById("instituteName") 
            ? document.getElementById("instituteName").outerHTML 
            : "";

        var newWin = window.open("", "_blank");
        newWin.document.write(`
            <html>
                <head>
                    <title>Cadre Summary</title>
                    <style>
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f2f2f2;
                        }
                        h5 {
                            margin-bottom: 15px;
                        }
                    </style>
                </head>
                <body>
                    ${instituteName}
                    ${divToPrint.outerHTML}
                </body>
            </html>
        `);
        newWin.document.close();
        newWin.focus();
        newWin.print();
        newWin.close();
    }


	document.getElementById('print').addEventListener('click', printData);

	document.getElementById('searchInput').addEventListener('keyup', function() {
        var searchValue = this.value.toLowerCase();
        var rows = document.querySelectorAll('#tableBody tr');

        rows.forEach(function(row) {
            var cells = row.getElementsByTagName('td');
            var match = Array.from(cells).some(function(cell) {
                return cell.textContent.toLowerCase().includes(searchValue);
            });

            row.style.display = match ? '' : 'none';
        });
    });
</script>
@endpush

		