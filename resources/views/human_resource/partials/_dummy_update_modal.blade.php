{{-- resources/views/employee/partials/_dummy_update_modal.blade.php --}}

@php
    // map of dummy keys => human label and guessed employee attribute
    $dummyMap = [
        'dummy_nicnew' => ['label' => 'NIC (New)', 'emp' => 'nicnew'],
        'dummy_title' => ['label' => 'Title', 'emp' => 'title'],
        'dummy_initial' => ['label' => 'Initial', 'emp' => 'initial'],
        'dummy_surname' => ['label' => 'Surname', 'emp' => 'surname'],
        'dummy_fullname' => ['label' => 'Full name', 'emp' => 'fullname'],
        'dummy_dob' => ['label' => 'Date of birth', 'emp' => 'dob'],
        'dummy_gender' => ['label' => 'Gender', 'emp' => 'gender'],
        'dummy_civilstatus' => ['label' => 'Civil status', 'emp' => 'civilstatus'],
        'dummy_ethinicity' => ['label' => 'Ethnicity', 'emp' => 'ethinicity'],
        'dummy_religion' => ['label' => 'Religion', 'emp' => 'religion'],
        'dummy_tmpaddress' => ['label' => 'Temporary address', 'emp' => 'tmpaddress'],
        'dummy_dsdivision_id' => ['label' => 'DS Division', 'emp' => 'dsdivision_id'],
        'dummy_gndivision_id' => ['label' => 'GN Division', 'emp' => 'gndivision_id'],
        'dummy_transmode_id' => ['label' => 'Transport mode', 'emp' => 'transmode_id'],
        'dummy_distores' => ['label' => 'Distance to residence', 'emp' => 'distores'],
        'dummy_mobile' => ['label' => 'Mobile', 'emp' => 'mobile'],
        'dummy_whatsapp' => ['label' => 'WhatsApp', 'emp' => 'whatsapp'],
        'dummy_fixedphone' => ['label' => 'Fixed phone', 'emp' => 'fixedphone'],
        'dummy_email' => ['label' => 'Email', 'emp' => 'email'],
        'dummy_empservice_id' => ['label' => 'Employment service', 'emp' => 'empservice_id'],
        'dummy_grade' => ['label' => 'Grade', 'emp' => 'grade'],
        'dummy_dtyasmfapp' => ['label' => 'DTY ASM FAPP', 'emp' => 'dtyasmfapp'],
        'dummy_dtyasmcser' => ['label' => 'DTY ASM CSER', 'emp' => 'dtyasmcser'],
        'dummy_designation_id' => ['label' => 'Designation', 'emp' => 'designation_id'],
        'dummy_institute_id' => ['label' => 'Institute', 'emp' => 'institute_id'],
        'dummy_current_working_station' => ['label' => 'Current working station', 'emp' => 'current_working_station'],
        'dummy_dtyasmprins' => ['label' => 'DTY ASM PRINS', 'emp' => 'dtyasmprins'],
        'dummy_highqualification_id' => ['label' => 'High qualification', 'emp' => 'highqualification_id'],
        'dummy_degree_id' => ['label' => 'Degree', 'emp' => 'degree_id'],
        'dummy_degtype' => ['label' => 'Degree type', 'emp' => 'degtype'],
        'dummy_degsubject1_id' => ['label' => 'Deg subject 1', 'emp' => 'degsubject1_id'],
        'dummy_degsubject2_id' => ['label' => 'Deg subject 2', 'emp' => 'degsubject2_id'],
        'dummy_degsubject3_id' => ['label' => 'Deg subject 3', 'emp' => 'degsubject3_id'],
        'dummy_appsubject' => ['label' => 'App subject', 'emp' => 'appsubject'],
        'dummy_appcategory_id' => ['label' => 'App category', 'emp' => 'appcategory_id'],
        'dummy_cadresubject_id' => ['label' => 'Cadre subject', 'emp' => 'cadresubject_id'],
        'dummy_trained' => ['label' => 'Trained', 'emp' => 'trained'],
        'dummy_remark' => ['label' => 'Remark', 'emp' => 'remark'],
    ];

    // helper to normalize values to strings for comparison
    $norm = function($v){
        if(is_null($v)) return '';
        if(is_bool($v)) return $v ? '1' : '0';
        if(is_array($v)) return json_encode($v);
        return (string)$v;
    };

    $changedRows = [];

    if(isset($employeeDummy) && $employeeDummy){
        foreach($dummyMap as $dummyKey => $meta){
            $empAttr = $meta['emp'];
            // dummy value: prefer direct property on $employeeDummy, otherwise via data_get
            $pending = data_get($employeeDummy, str_replace('dummy_','', $dummyKey));
            // fallback: also try direct property if keys match exactly
            if($pending === null){
                $pending = data_get($employeeDummy, $dummyKey);
            }

            $current = data_get($employee, $empAttr);

            // normalize both and compare
            if($norm($current) !== $norm($pending)){
                $changedRows[$dummyKey] = [
                    'label' => $meta['label'],
                    'current' => $current,
                    'pending' => $pending
                ];
            }
        }
    }
@endphp

<!-- Modal -->
<div class="modal fade" id="dummyUpdateModal" tabindex="-1" aria-labelledby="dummyUpdateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-height: 90vh;">
    <div class="modal-content">

      <form id="dummyUpdateForm" method="POST" action="{{ route('employee.update', $employee->id ?? 0) }}">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title" id="dummyUpdateModalLabel">Pending changes (apply selected)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <!-- BODY (scrollable area) -->
        <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">

          @if(empty($changedRows))
            <div class="alert alert-info">No pending changes to apply.</div>
          @else

            <p>Only fields that are different from the current record are shown. Tick to apply.</p>

            <div class="table-responsive">
              <table class="table table-sm table-bordered">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                  <tr>
                    <th style="width:40px">#</th>
                    <th style="width:40px">Apply?</th>
                    <th>Field</th>
                    <th>Current value</th>
                    <th>Pending value</th>
                  </tr>
                </thead>
                <tbody>
                  @php $i = 1; @endphp
                  @foreach($changedRows as $dummyKey => $row)
                    <tr class="dummy-row" data-field="{{ $dummyKey }}">
                      <td>{{ $i }}</td>

                      <td class="text-center align-middle">
                        <input type="checkbox" name="update[{{ $dummyKey }}]" value="1"
                               class="form-check-input update-checkbox"
                               id="chk_{{ $dummyKey }}">
                      </td>

                      <td class="align-middle"><strong>{{ $row['label'] }}</strong></td>

                      <td class="align-middle">
                        <small class="text-muted">{{ $row['current'] ?? '—' }}</small>
                      </td>

                      <td class="align-middle">
                        <input type="text"
                               class="form-control form-control-sm pending-input"
                               name="{{ $dummyKey }}"
                               value="{{ $row['pending'] }}">
                      </td>
                    </tr>
                    @php $i++; @endphp
                  @endforeach
                </tbody>
              </table>
            </div>

          @endif

        </div>
        <!-- END BODY -->

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

          {{-- Ignore all: remove the pending dummy row entirely for this employee --}}
          @if(!empty($employee))
            <button type="button"
                    id="ignoreDummyBtn"
                    data-employee-id="{{ $employee->id }}"
                    class="btn btn-danger">
              Ignore all
            </button>
          @endif

          @if(!empty($changedRows))
            <button type="submit" class="btn btn-primary" id="applyDummyBtn">Apply selected updates</button>
          @endif
        </div>

      </form>

    </div>
  </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  console.log('Bootstrap modal plugin:', $.fn.modal ? 'present' : 'NOT present');
console.log('jQuery version:', $.fn.jquery);
  const modalForm = document.getElementById('dummyUpdateForm');
  const mainForm  = document.getElementById('employee_form'); // main edit form

  if (!modalForm) return;

  // Visual toggle when checking
  modalForm.querySelectorAll('.update-checkbox').forEach(function(chk){
    chk.addEventListener('change', function(){
      const tr = chk.closest('tr');
      if(chk.checked) tr.classList.add('table-success'); else tr.classList.remove('table-success');
    });
  });

  modalForm.addEventListener('submit', function(e){
    // 1) Quick debug snapshot (initial)
    const fdDebug = new FormData(modalForm);
    const debug = {};
    fdDebug.forEach((v,k) => {
      if (Object.prototype.hasOwnProperty.call(debug, k)) {
        if (!Array.isArray(debug[k])) debug[k] = [debug[k]];
        debug[k].push(v);
      } else debug[k] = v;
    });
    console.log('Before submit - initial modal data ->', debug);

    // 2) Require at least one checkbox
    const checkedBoxes = modalForm.querySelectorAll('input.update-checkbox:checked');
    // if (checkedBoxes.length === 0) {
    //   e.preventDefault();
    //   alert('Please tick at least one field to apply.');
    //   return false;
    // }

    // 3) Ensure update[...] hidden fallback exists and enable pending inputs for checked rows
    checkedBoxes.forEach(function(chk){
      const row = chk.closest('.dummy-row');
      const field = row?.getAttribute('data-field');
      if (!field) return;

      // hidden fallback
      if (!modalForm.querySelector('input[type="hidden"][name="update['+field+']"]')) {
        const h = document.createElement('input');
        h.type = 'hidden';
        h.name = 'update['+field+']';
        h.value = '1';
        modalForm.appendChild(h);
      }

      // ensure pending input enabled
      const pending = modalForm.querySelector('input.pending-input[name="'+field+'"]');
      if (pending) pending.disabled = false;
    });

    // 4) Disable pending inputs for unchecked rows (so they don't get sent)
    modalForm.querySelectorAll('.dummy-row').forEach(function(row) {
      const field = row.getAttribute('data-field');
      const chk = row.querySelector('#chk_' + field);
      const input = row.querySelector('input.pending-input');
      if (input && (!chk || !chk.checked)) input.disabled = true;
    });

    // 5) COPY main form fields into modal form as hidden inputs
    if (mainForm) {
      // list of names to skip (we already have _token and modal form _method)
      const skipNames = new Set(['_token','_method','update']);
      // also skip dummy_* names (they are already in modal)
      modalForm.querySelectorAll('input.pending-input[name]').forEach(function(pi){
        skipNames.add(pi.name);
      });

      // Temporarily enable disabled main form elements so we can read values
      const mainEls = Array.from(mainForm.elements);
      const disabledEls = mainEls.filter(el => el.disabled);
      disabledEls.forEach(el => el.disabled = false);

      mainEls.forEach(function(el){
        if (!el.name) return;
        if (skipNames.has(el.name)) return;
        if (el.type === 'file') return; // skip files
        if (el.type === 'button' || el.type === 'submit' || el.type === 'reset') return;

        // checkboxes/radios - only include checked ones
        if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;

        // select[multiple]
        if (el.tagName.toLowerCase() === 'select' && el.multiple) {
          Array.from(el.selectedOptions).forEach(opt => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = el.name; // preserves [] if present
            hidden.value = opt.value;
            modalForm.appendChild(hidden);
          });
          return;
        }

        // arrays like name[] - append multiple hidden inputs as-is
        if (el.name.endsWith('[]')) {
          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = el.name;
          hidden.value = el.value;
          modalForm.appendChild(hidden);
          return;
        }

        // normal input/select
        // Avoid duplicating _token/_method
        if (['_token','_method'].includes(el.name)) return;

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = el.name;
        hidden.value = el.value;
        modalForm.appendChild(hidden);
      });

      // restore disabled state
      disabledEls.forEach(el => el.disabled = true);
    }

    // 6) Final payload log (very helpful)
    const fd = new FormData(modalForm);
    const out = {};
    fd.forEach((v,k) => {
      if (Object.prototype.hasOwnProperty.call(out, k)) {
        if (!Array.isArray(out[k])) out[k] = [out[k]];
        out[k].push(v);
      } else out[k] = v;
    });
    console.log('Final payload to be submitted ->', out);

    // allow submit to proceed
  });

  //Ignore all dummy changes 
  const ignoreBtn = document.getElementById('ignoreDummyBtn');
  if (!ignoreBtn) return;

  ignoreBtn.addEventListener('click', function (ev) {
    ev.preventDefault();

    const empId = this.getAttribute('data-employee-id');
    if (!empId) return alert('Employee id missing.');

    if (!confirm('Are you sure you want to ignore all pending changes for this employee? This will remove the pending record.')) {
      return;
    }

    // get CSRF token from meta
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/employee/${empId}/dummy-ignore`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({}) // no body required, but some servers like a body for DELETE
    })
    .then(async (res) => {
      const data = await res.json().catch(() => ({}));
      if (res.ok) {
        // 1) ask Bootstrap to hide (normal way)
        try {
          $('#dummyUpdateModal').modal('hide');
        } catch (err) {
          console.warn('modal.hide() threw:', err);
        }

        // 2) small delay then force-clean leftovers
        setTimeout(function(){
          // remove any backdrop nodes
          $('.modal-backdrop').remove();

          $('body').removeClass('modal-open');

          $('#dummyUpdateModal').hide().removeClass('show').attr('aria-hidden','true').css('display','none');

          $('#dummyUpdateModal').closest('.modal').removeAttr('style');

          $(document.activeElement).blur();
          $('body').focus();
        }, 200);

        alert(data.success || 'Pending changes removed.');
        
        // **Reload main edit page**
        setTimeout(function(){
            location.reload();
        }, 300); // small delay for smooth closing
      } else {
        alert(data.error || data.warning || 'Failed to ignore pending changes.');
      }

    })
    .catch(err => {
      console.error('Ignore dummy failed', err);
      alert('Unexpected error while ignoring pending changes.');
    });
  });
});
</script>
@endpush
