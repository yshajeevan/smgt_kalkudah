@extends('layouts.master')

@section('main-content')

<div class="flash-message text-center">
  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))
      <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
    @endif
  @endforeach
</div>

<div class="container p-3">
    <div class="card-heading">
        <h5>{{ $stupop->institute->institute ?? '' }}</h5>
        <p>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
    </div>

    <form method="POST" action="{{ route('attendance.store') }}">
    @csrf

    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <tr>
                <th style="width:40%">Category</th>
                <th style="width:30%">Stu No</th>
            </tr>

            {{-- Grade 1-5 --}}
            @if($stupop && $stupop->{"1_5_tot"} >= 1)
            <tr class="group-row">
                <td colspan="2">Primary</td>
            </tr>
            <tr>
                <td>Grade 1 - 5</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="1_5_pr"
                               data-max="{{ $stupop->{'1_5_tot'} }}"
                               value="{{ old('1_5_pr') }}" min="0" max="{{ $stupop->{'1_5_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('1_5_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"1_5_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'1_5_tot'} }})</p>
                    <input type="hidden" name="1_5_tot" value="{{ $stupop->{'1_5_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- Grade 6-9 --}}
            @if($stupop && $stupop->{"6_9_tot"} >= 1)
            <tr class="group-row">
                <td colspan="2">Junior Secondary</td>
            </tr>
            <tr>
                <td>Grade 6 - 9</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="6_9_pr"
                               data-max="{{ $stupop->{'6_9_tot'} }}"
                               value="{{ old('6_9_pr') }}" min="0" max="{{ $stupop->{'6_9_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('6_9_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"6_9_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'6_9_tot'} }})</p>
                    <input type="hidden" name="6_9_tot" value="{{ $stupop->{'6_9_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- Grade 10-11 --}}
            @if($stupop && $stupop->{"10_11_tot"} >= 1)
            <tr>
                <td>Grade 10 - 11</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="10_11_pr"
                               data-max="{{ $stupop->{'10_11_tot'} }}"
                               value="{{ old('10_11_pr') }}" min="0" max="{{ $stupop->{'10_11_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('10_11_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"10_11_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'10_11_tot'} }})</p>
                    <input type="hidden" name="10_11_tot" value="{{ $stupop->{'10_11_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Arts 1st --}}
            @if($stupop && $stupop->{"al_arts_1st_tot"} >= 1)
            <tr class="group-row">
                <td colspan="2">A/L 1st Year</td>
            </tr>
            <tr>
                <td>A/L Arts 1st Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_arts_1st_pr"
                               data-max="{{ $stupop->{'al_arts_1st_tot'} }}"
                               value="{{ old('al_arts_1st_pr') }}" min="0" max="{{ $stupop->{'al_arts_1st_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_arts_1st_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_arts_1st_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_arts_1st_tot'} }})</p>
                    <input type="hidden" name="al_arts_1st_tot" value="{{ $stupop->{'al_arts_1st_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Commerce 1st --}}
            @if($stupop && $stupop->{"al_com_1st_tot"} >= 1)
            <tr>
                <td>A/L Commerce 1st Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_com_1st_pr"
                               data-max="{{ $stupop->{'al_com_1st_tot'} }}"
                               value="{{ old('al_com_1st_pr') }}" min="0" max="{{ $stupop->{'al_com_1st_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_com_1st_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_com_1st_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_com_1st_tot'} }})</p>
                    <input type="hidden" name="al_com_1st_tot" value="{{ $stupop->{'al_com_1st_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Ph.Science 1st --}}
            @if($stupop && $stupop->{"al_physc_1st_tot"} >= 1)
            <tr>
                <td>A/L Ph.Science 1st Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_physc_1st_pr"
                               data-max="{{ $stupop->{'al_physc_1st_tot'} }}"
                               value="{{ old('al_physc_1st_pr') }}" min="0" max="{{ $stupop->{'al_physc_1st_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_physc_1st_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_physc_1st_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_physc_1st_tot'} }})</p>
                    <input type="hidden" name="al_physc_1st_tot" value="{{ $stupop->{'al_physc_1st_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Bio.Science 1st --}}
            @if($stupop && $stupop->{"al_biosc_1st_tot"} >= 1)
            <tr>
                <td>A/L Bio.Science 1st Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_biosc_1st_pr"
                               data-max="{{ $stupop->{'al_biosc_1st_tot'} }}"
                               value="{{ old('al_biosc_1st_pr') }}" min="0" max="{{ $stupop->{'al_biosc_1st_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_biosc_1st_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_biosc_1st_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_biosc_1st_tot'} }})</p>
                    <input type="hidden" name="al_biosc_1st_tot" value="{{ $stupop->{'al_biosc_1st_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L ETech 1st --}}
            @if($stupop && $stupop->{"al_etech_1st_tot"} >= 1)
            <tr>
                <td>A/L ETech 1st Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_etech_1st_pr"
                               data-max="{{ $stupop->{'al_etech_1st_tot'} }}"
                               value="{{ old('al_etech_1st_pr') }}" min="0" max="{{ $stupop->{'al_etech_1st_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_etech_1st_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_etech_1st_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_etech_1st_tot'} }})</p>
                    <input type="hidden" name="al_etech_1st_tot" value="{{ $stupop->{'al_etech_1st_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L BTech 1st --}}
            @if($stupop && $stupop->{"al_btech_1st_tot"} >= 1)
            <tr>
                <td>A/L BTech 1st Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_btech_1st_pr"
                               data-max="{{ $stupop->{'al_btech_1st_tot'} }}"
                               value="{{ old('al_btech_1st_pr') }}" min="0" max="{{ $stupop->{'al_btech_1st_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_btech_1st_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_btech_1st_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_btech_1st_tot'} }})</p>
                    <input type="hidden" name="al_btech_1st_tot" value="{{ $stupop->{'al_btech_1st_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Arts 2nd --}}
            @if($stupop && $stupop->{"al_arts_2nd_tot"} >= 1)
            <tr class="group-row">
                <td colspan="2">A/L 2nd Year</td>
            </tr>
            <tr>
                <td>A/L Arts 2nd Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_arts_2nd_pr"
                               data-max="{{ $stupop->{'al_arts_2nd_tot'} }}"
                               value="{{ old('al_arts_2nd_pr') }}" min="0" max="{{ $stupop->{'al_arts_2nd_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_arts_2nd_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_arts_2nd_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_arts_2nd_tot'} }})</p>
                    <input type="hidden" name="al_arts_2nd_tot" value="{{ $stupop->{'al_arts_2nd_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Commerce 2nd --}}
            @if($stupop && $stupop->{"al_com_2nd_tot"} >= 1)
            <tr>
                <td>A/L Commerce 2nd Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_com_2nd_pr"
                               data-max="{{ $stupop->{'al_com_2nd_tot'} }}"
                               value="{{ old('al_com_2nd_pr') }}" min="0" max="{{ $stupop->{'al_com_2nd_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_com_2nd_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_com_2nd_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_com_2nd_tot'} }})</p>
                    <input type="hidden" name="al_com_2nd_tot" value="{{ $stupop->{'al_com_2nd_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Physical Science 2nd --}}
            @if($stupop && $stupop->{"al_physc_2nd_tot"} >= 1)
            <tr>
                <td>A/L Physical Science 2nd Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_physc_2nd_pr"
                               data-max="{{ $stupop->{'al_physc_2nd_tot'} }}"
                               value="{{ old('al_physc_2nd_pr') }}" min="0" max="{{ $stupop->{'al_physc_2nd_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_physc_2nd_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_physc_2nd_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_physc_2nd_tot'} }})</p>
                    <input type="hidden" name="al_physc_2nd_tot" value="{{ $stupop->{'al_physc_2nd_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L Bio Science 2nd --}}
            @if($stupop && $stupop->{"al_biosc_2nd_tot"} >= 1)
            <tr>
                <td>A/L Bio Science 2nd Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_biosc_2nd_pr"
                               data-max="{{ $stupop->{'al_biosc_2nd_tot'} }}"
                               value="{{ old('al_biosc_2nd_pr') }}" min="0" max="{{ $stupop->{'al_biosc_2nd_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_biosc_2nd_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_biosc_2nd_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_biosc_2nd_tot'} }})</p>
                    <input type="hidden" name="al_biosc_2nd_tot" value="{{ $stupop->{'al_biosc_2nd_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L ETech 2nd --}}
            @if($stupop && $stupop->{"al_etech_2nd_tot"} >= 1)
            <tr>
                <td>A/L ETech 2nd Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_etech_2nd_pr"
                               data-max="{{ $stupop->{'al_etech_2nd_tot'} }}"
                               value="{{ old('al_etech_2nd_pr') }}" min="0" max="{{ $stupop->{'al_etech_2nd_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_etech_2nd_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_etech_2nd_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_etech_2nd_tot'} }})</p>
                    <input type="hidden" name="al_etech_2nd_tot" value="{{ $stupop->{'al_etech_2nd_tot'} }}">
                </td>
            </tr>
            @endif

            {{-- A/L BTech 2nd --}}
            @if($stupop && $stupop->{"al_btech_2nd_tot"} >= 1)
            <tr>
                <td>A/L BTech 2nd Year</td>
                <td>
                    <div class="input-wrap">
                        <input type="number" class="textbox pr-input" name="al_btech_2nd_pr"
                               data-max="{{ $stupop->{'al_btech_2nd_tot'} }}"
                               value="{{ old('al_btech_2nd_pr') }}" min="0" max="{{ $stupop->{'al_btech_2nd_tot'} }}">
                        <span class="tick-wrap">
                            <svg class="tick-svg" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <polyline class="tick-line" points="4,13 9,18 20,6"/>
                            </svg>
                        </span>
                    </div>
                    @error('al_btech_2nd_pr') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Total: {{ $stupop->{"al_btech_2nd_tot"} }}</p>
                    <p class="exceed-error" style="display:none;">Cannot exceed total ({{ $stupop->{'al_btech_2nd_tot'} }})</p>
                    <input type="hidden" name="al_btech_2nd_tot" value="{{ $stupop->{'al_btech_2nd_tot'} }}">
                </td>
            </tr>
            @endif
            
            {{-- Teachers --}}
            <tr class="group-row">
                <td colspan="2">Staffr</td>
            </tr>
            <tr>
                <td>Teachers</td>

                <td colspan="2"> {{-- merge 2 columns for clean layout --}}
                    <div style="display:flex; justify-content:center; gap:6px;">

                        {{-- TOTAL --}}
                        <div class="text-center">
                            <div class="input-wrap">
                                <input type="number"
                                    class="textbox"
                                    name="tottea"
                                    id="tottea"
                                    value="{{ old('tottea', $attendance->tottea ?? '') }}"
                                    min="0"
                                    style="width:50px;">

                                <span class="tick-wrap">
                                    <svg class="tick-svg" viewBox="0 0 24 24">
                                        <polyline class="tick-line" points="4,13 9,18 20,6"/>
                                    </svg>
                                </span>
                            </div>

                            <p class="hint">Total Teachers</p>
                        </div>

                        {{-- PRESENT --}}
                        <div class="text-center">
                            <div class="input-wrap">
                                <input type="number"
                                    class="textbox pr-input"
                                    name="prtea"
                                    id="prtea"
                                    value="{{ old('prtea', $attendance->prtea ?? '') }}"
                                    min="0"
                                    style="width:50px;">

                                <span class="tick-wrap">
                                    <svg class="tick-svg" viewBox="0 0 24 24">
                                        <polyline class="tick-line" points="4,13 9,18 20,6"/>
                                    </svg>
                                </span>
                            </div>

                            <p class="hint">
                                Total: <span id="total_hint">{{ $attendance->tottea ?? 0 }}</span>
                            </p>

                            <p class="exceed-error" id="teacher_error" style="display:none;">
                                Cannot exceed total
                            </p>
                        </div>

                    </div>
                </td>
            </tr>

            {{-- Principal --}}
            <tr>
                <td>Principal</td>
                <td colspan="2">
                    <label>
                        <input type="radio" name="principal" value="1" {{ old('principal',1)==1?'checked':'' }}> On-duty
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="principal" value="2" {{ old('principal')==='0'?'checked':'' }}> Duty Leave
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="principal" value="3" {{ old('principal')==='0'?'checked':'' }}> Personel Leave
                    </label>
                </td>
            </tr>

        </table>

        <div class="text-center">
            <input type="hidden" name="institute_id" value="{{ $instid }}">
            <button type="submit" class="btn btn-success">Save</button>
        </div>

    </div>
    </form>
</div>

@endsection

@push('styles')
<style>
.error {
    font-size: 13px;
    color: red;
    margin: 2px 0;
}

.group-row td {
    background: #f1f3f5;
    font-weight: 600;
    text-align: center;
    padding: 1px 1px;
    border-top: 2px solid #dee2e6;
}

.table td, .table th {
    padding: 1px !important;
    vertical-align: middle;
}


.textbox {
    width: 70px;   /* reduce width */
    height: 36px;  /* compact */
    padding: 4px;
    text-align: right;
    border-radius: 8px;
    border: 1px solid #ccc;
    transition: border-color 0.25s;
}

.textbox.is-valid {
    border-color: #28a745;
}

.textbox.is-invalid {
    border-color: #dc3545;
}

.container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.hint {
    color: #6c757d;
    font-size: 12px;
    font-style: italic;
    margin: 2px 0 0;
}

.card-heading {
    text-align: center;
    margin-bottom: 10px;
}

.exceed-error {
    font-size: 12px;
    color: #dc3545;
    margin: 2px 0;
}

/* ---- Tick SVG ---- */
.input-wrap {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: 100%;
    justify-content: center;
    margin-bottom: 1px;
}

.tick-wrap {
    display: inline-flex;
    align-items: center;
    width: 22px;
    height: 22px;
    flex-shrink: 0;
}

.tick-svg {
    width: 22px;
    height: 22px;
    display: none;
}

.tick-line {
    stroke: #28a745;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
    fill: none;
    stroke-dasharray: 28;
    stroke-dashoffset: 28;
}

/* When visible, animate */
.tick-svg.show {
    display: block;
}

.tick-svg.animate .tick-line {
    animation: drawTick 0.35s ease forwards;
}


@keyframes drawTick {
    to {
        stroke-dashoffset: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // --- Flash alert ---
    @if(Session::has('alert'))
        alert('{{ Session::get('alert') }}');
    @endif

    // --- PR input validation & tick logic ---
    document.querySelectorAll('.pr-input').forEach(function(input) {
        input.addEventListener('input', function() {
            validateInput(this);
        });

        // Run on page load for old() values
        if (input.value !== '') {
            validateInput(input);
        }
    });

    function validateInput(input) {
        var max      = parseInt(input.getAttribute('data-max'), 10);
        var val      = parseInt(input.value, 10);
        var td       = input.closest('td');
        var tickSvg  = td.querySelector('.tick-svg');
        var tickLine = td.querySelector('.tick-line');
        var exceedEl = td.querySelector('.exceed-error');

        // Empty — reset
        if (input.value === '' || isNaN(val)) {
            input.classList.remove('is-valid', 'is-invalid');
            tickSvg.classList.remove('show', 'animate');
            if (exceedEl) exceedEl.style.display = 'none';
            return;
        }

        if (val > max) {
            // Exceeds total
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            tickSvg.classList.remove('show', 'animate');
            if (exceedEl) exceedEl.style.display = 'block';
        } else if (val >= 0) {
            // Valid
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            if (exceedEl) exceedEl.style.display = 'none';

            // Re-trigger animation by removing then adding classes
            tickSvg.classList.remove('animate');
            tickSvg.classList.add('show');
            // Force reflow so animation restarts
            void tickSvg.offsetWidth;
            tickLine.style.strokeDashoffset = '28';
            tickSvg.classList.add('animate');
        }
    }

    $(document).on('input', '#tottea', function () {
        let total = parseInt($(this).val()) || 0;
        $('#total_hint').text(total);
    });

    $(document).on('input', '#prtea', function () {
        let total = parseInt($('#tottea').val()) || 0;
        let present = parseInt($(this).val()) || 0;

        if (present > total) {
            $('#teacher_error').show();
            $(this).val(total);
        } else {
            $('#teacher_error').hide();
        }
    });
</script>
@endpush
