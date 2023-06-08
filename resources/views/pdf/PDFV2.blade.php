

@if(in_array('CATV',$page_list) === true || in_array('DSTB',$page_list) === true)
    @include('pdf.table_dstb')
@endif

@if(in_array('TWMBB',$page_list) === true)
    @include('pdf.table_twmbb')
@endif

@if(in_array('TWMBB_789',$page_list) === true)
    @include('pdf.table_twmbb_789')
@endif

@if(in_array('CM',$page_list) === true)
    @include('pdf.table_cm')
@endif

@if(in_array('FTTB',$page_list) === true)
    @include('pdf.table_fttb')
@endif

@if(in_array('FTTH',$page_list) === true)
    @include('pdf.table_ftth')
@endif

{{--條款[提示] 不含維修--}}
@if(in_array('MAINTAIN',$page_list) === false)
    @if(in_array('DEVICEGET',$page_list) === false)
        @include('pdf.termsSpecial')
    @endif
@endif

@if(in_array('MAINTAIN',$page_list) === true)
    @include('pdf.table_maintain')
@endif

@if(in_array('DEVICEGET',$page_list) === true)
    @include('pdf.table_deviceget')
@endif

@if(in_array('CUSTODY',$page_list) === true)
    @include('pdf.table_custody')
@endif

{{--@if(in_array('TERMS',$page_list) === true)--}}
{{--    @include("pdf.terms".$data['comapnyno'])--}}
{{--@endif--}}

<div class="w-100">
    <p class="text-right font-s8">C{{ date('Ymd-His') }}</p>
</div>
