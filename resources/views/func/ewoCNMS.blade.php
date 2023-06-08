@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main>
    <div class="container bg-grey">
        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    CMNS
                </div>
            </div>
            <div class="card-body">
            </div>
        </div>
    </div>

    {{-- LoadIng --}}
    <div class="modal fade" id="loadIng" role="dialog" >
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="showStr">
                            資料查詢中...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

@endsection

@section('script')
<script>

    $(document).ready(function () {
        $('#loadIng').modal('show');
        getCNMS();
        setTimeout(function(){
            location.href = '/ewo/func';
        },2000);
    });
    /*********** Redy end *************/


    function getCNMS(){
        let url = 'https://cmweb.bbtv.tw/cmweb/login?redirect=https%3A%2F%2Fcmweb.bbtv.tw%2Fcmweb%2Findex';
        app.openUrl(url);
    }


    function modalClose() {
        for(let i=1; i<10; i++) {
            setTimeout(function(){
                $('#loadIng').modal('hide');
            },1000);
        }
    }


</script>
@endsection
