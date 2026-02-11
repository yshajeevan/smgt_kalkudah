<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Service Management System of Zonal Education Office, Batticaloa West">
<meta name="author" content="Shajeevan(SLEAS-III">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SMGT</title>
<link rel="icon" type="image/x-icon" href="{{asset('/backend/img/favicon.ico')}}">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
<!-- Bootstrap core JavaScript-->
<script src="{{asset('backend/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('backend/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{asset('backend/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <script src="{{asset('backend/js/print.js')}}"></script>
</head>
<style>
    body {
       width: 230mm;
       height: 100%;
       margin: 0;
       padding: 0;
       background: rgb(204,204,204); 
     }
     * {
       box-sizing: border-box;
       -moz-box-sizing: border-box;
     }
    .main-page {
       width: 210mm;
       min-height: 290mm;
       margin: 10mm auto;
       background: white;
       box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
     }
    .sub-page {
       padding: 0;
       height: 290mm;
     }
    .letter-head{
        height:35mm;
    } 
    .box {
        height:35mm; 
        display: flex;
        align-items: center;
        text-align: center;
        justify-content: center;
        width:100%;
    }
    .logo-container{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .logo-right{
        height:35mm; 
        width:auto;
    }  
    .logo-left{
        height:30mm; 
        width:auto;
    } 
    hr.header-line {
        border: 1px solid red;
    }
    hr.footer-line {
        margin: 0;
        border: 1px solid red;
    }
    .main-content{
        height: 235mm !important;
        padding-left: 15mm !important;
        padding-right: 05mm !important;
    }
    .letter-info{
        font-size:0.9em;
        z-index=:99;
        margin-top:-40px;
    }
    .letter-address p{
        font-size:0.8em;
        margin-bottom:0;
    }
    .letter-content h6{
        font-size:0.9em !important;
    }
    .letter-content p{
        font-size:0.7em;
        text-align: justify;
        text-justify: inter-word;
    }
    .location{
        margin-top:0;
        font-size:0.8em;
    }
    .signature-div{
        margin-top:1mm;
        font-size:0.7em;
        width:100%;
    }
    .e-signature{
        height:10mm;
    }
    .not-singed{
        width:30mm;
        height:auto;
    }
    .corbon-copy p{
        font-size:0.7em;
        margin-bottom:0;
        width:100%;
    }
    .letter-footer p{
        margin-bottom:0;
        font-size:0.8em;
        padding-left: 15mm;
        padding-right: 05mm;
    }
    @page {
       size: A4;
       margin: 0;
    }
    @media print {
       html, body {
     	width: 210mm;
     	height: 297mm;        
    }
    .main-page {
     	margin: 0 !important;
     	border: initial;
     	border-radius: initial;
     	width: initial;
     	min-height: initial;
     	box-shadow: initial;
     	background: initial;
     	page-break-after: always;
       }
    }

</style>
<body>
    <div class="main-page">
        <div id="printElement"> 
            <div class="sub-page">
                <div class="row letter-head">
                    <div class="col-3 logo-container">
                        <img src="https://smgt.battiwestzeo.lk/images/government_logo.png" class="logo-left" alt="" border="0">
                    </div>
                    <div class="col-6">
                        <div class="box">
                            <h5>வலயக்கல்வி அலுவலகம்,  மட்டக்களப்பு மேற்கு<br>
                                කලාප අධ්‍යාපන කාර්යාලය, මඩකලපුව බටහිර <br>
                                Zonal Education Office, Batticaloa West</h4>
                        </div>
                    </div>
                    <div class="col-3 logo-container">
                        <img src="https://smgt.battiwestzeo.lk/images/bwzeo_logo.jpg" class="logo-right" alt="" border="0"> 
                    </div>
                </div>
                <hr class="header-line">
                <div class="main-content">
                    <div class="letter-info">
                        <div class="row">
                            <div class="col-6">
                                <p class="float-left font-weight-bold">My No: BT/BW/ZEO/@if($transfer->employee->empno < 8000){{$transfer->employee->empno}}@else Not Assigned @endif/REF-{{$transfer->process_id}}</p>
                            </div>
                            <div class="col-6">
                                <p class="float-right font-weight-bold" style="padding-right:14px;">Date: {{$transfer->created_at->format('d-m-Y')}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row letter-address" style="margin-bottom:0;">
                        <div class="col-12">
                            <p>{{$transfer->employee->title.".".$transfer->employee->initial.".".$transfer->employee->surname}}</p>
                            <p>{{$transfer->employee->nic}}</p>
                            <p>{{$transfer->institute1->institute}}</p>
                            <p>{{$transfer->employee->designation->designation."(".$transfer->employee->cadresubject->cadre.")"}}</p>
                        </div>
                    </div><br>
                    <div class="row letter-content">
                        <div class="col-12">
                            <h6 class="font-weight-bold" style="margin-bottom:0;"> @if($transfer->transfer_type == 3) வருடாந்த ஆசிரியர் இடமாற்றம்-{{Carbon\Carbon::now()->format('Y')}}  @elseif($transfer->transfer_type == 0) பாடசாலையின் தேவை கருதிய இடமாற்றம் @elseif($transfer->transfer_type == 1) பாடசாலையின் தேவை கருதிய தற்காலிக இடமாற்றம் @elseif ($transfer->transfer_type == 2) பாடசாலையின் தேவை கருதிய இரண்டு நாள் இடமாற்றம் @endif</h6><br>
                            <p>@if($transfer->transfer_type == 3) மேற்படி வலயத்திற்குள்ளான அனைத்து ஆசிரியர் சங்கங்களையும் உள்ளடக்கியதான வருடாந்த ஆசிரியர் இடமாற்ற தீர்மானக்தின் பிரகாரம் தங்களுக்கு @else பாடசாலையின் அவசிய தேவை கருதி தங்களுக்கு @endif {{date('d-m-Y', strtotime($transfer->effect_from))}} திகதி முதல் செயற்படும் வண்ணம் @if($transfer->transfer_type == 2) மறு அறிவித்தல் வரை @endif கீழ்வரும் பாடசாலைக்கு @if($transfer->transfer_type == 1) தற்காலிகமாக @elseif ($transfer->transfer_type == 2) இரண்டு நாட்களுக்கு @endif இடமாற்றம் வழங்கப்படுகின்றது.</p>                        
                            <p class="font-weight-bold" style="font-size:0.9em; margin-top:0; margin-bottom:3mm;">{{$transfer->institute->institute}}<span class="location">{{"  (GPS Location: ".$transfer->institute->gpslocation.")"}}</span></p>
                            
                            <p>இதற்கமைய குறித்த தினத்தில் கடமையேற்று கடமையேற்ற கடிதத்தை தங்கள் பாடசாலை அதிபர் ஊடாக எனக்கும் இதில் பிரதியிடப்பட்டுள்ளவர்களுக்கும் அறியத்தரவும். இக்கடிதத்தில் குறிப்பிடப்பட்டுள்ள கோவை இலக்கத்தையும் திகதியையும் கடமையேற்றமை பற்றி அறிவிக்கும் கடிதத்தில் தவறாது குறிப்பிடவும்.</p>
                            <p>@if($transfer->transfer_type == 0 || $transfer->transfer_type == 1 || $transfer->transfer_type == 3) நீங்கள் புதிய பாடசாலையில் கடமையேற்பதற்கு முன்னர் தாங்கள் முன்னர் கடமையாற்றிய பாடசாலையில் உங்கள் பொறுப்பிலுள்ள சகல பாடசாலைப் பொறுப்புக்களையும் ஆவணங்களையும் பொருட்களையும் அதிபரிடம் அல்லது அவரால் பணிக்கப்படுபவரிடம் ஒப்படைத்து அது பற்றி எழுத்து மூலமான சான்றிதழைப் பெற்றுக்கொள்வதுடன் கடமையேற்றல் கடிதத்துடன் அதன் பிரதியினையும் இணைத்து அனுப்புமாறு வேண்டப்படுகின்றீர்கள்.@endif</p>
                            <p>@if($transfer->transfer_type == 3)குறித்த இடமாற்றம் தொடர்பாக தங்களுக்கு மேன்முறையீடுகள் எதும் இருப்பின் 14 நாட்களுக்குள் விண்ணப்பிக்க வேண்டும்.@endif</p> 
                        </div>
                    </div>
                    <div class="row signature-div float-left">
                        <div class="col-12">
                            @if($transfer->is_approved == 1 && $transfer->employee->institute1->pfclerk->id == Auth::user()->id)
                                <div class="e-signature"></div>
                            @else
                                <img src="https://smgt.battiwestzeo.lk/images/not_singed.png" class="not-singed" alt="" border="0">
                            @endif
                            <p>வலயக்கல்வி பணிப்பாளர்<br>
                            வலயக்கல்வி அலுவலகம்<br>
                            மட்டக்களப்பு மேற்கு</p>
                        </div>
                    </div>
                    <br>
                    <div class="row corbon-copy float-left">
                        <div class="col-12">
                            <p>பிரதி:</p>
                            <P>1. பிரதிக்கல்வி பணிப்பாளர்-திட்டமிடல், வலயக்கல்வி அலுவலகம், மட்டக்களப்பு மேற்கு</P>               
                            <P>2. கணக்காளர், வலயக்கல்வி அலுவலகம், மட்டக்களப்பு மேற்கு</P>              
                            <p>3. அதிபர், {{$transfer->institute1->institute}}</p>
                            <p>4. அதிபர், {{$transfer->institute->institute}}</p>
                            <p>5. சுய விபரக்கோவை</p>
                        </div>
                    </div>
                </div>
                <hr class="footer-line">
                <div class="letter-footer">
                    <div class="row ">
                        <div class="col-6">
                            <P>Address: Kurinchamunai, Kannankudah</P>
                            <P>email: baw@edudept.ep.gov.lk</P>
                            <p>Website: www.battiwestzeo.lk</p>
                        </div>
                        <div class="col-6" style="padding-left:80px;">
                            <P>General Number: +94652052983</P>
                            <P>Admin: +94652052986</P>
                            <p>Fax: +94652052983</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align: center;">
            <button class="btn btn-primary" id="printButton">Print</button>
        </div>
    </div>
</body>



<script>
function print() {
	printJS({
    printable: 'printElement',
    type: 'html',
    targetStyles: ['*']
 })
}

document.getElementById('printButton').addEventListener ("click", print)

</script>

