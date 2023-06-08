@extends('func.layouts.default')

@section('title', '英雄帖')

@section('content')
<style>
    .bg-ffaec9 { background-color: #ffaec9; }
</style>
<main>
    <div class="container bg-grey">
        <label class="alert alert-success w-100 mt-3 mb-2" id="title">
            疑難雜症分享
        </label>
        <div class="card">

            <div class="card-header">
                <div class="form-group mb-1">
                    <label>帖子(新增修改)</label>
                    <select class="custom-select pl-0" id="item">
                        <option data-id="0">新增</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <form class="p-3" action="/api/EWO/addOpinion" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="id">
                    <div class="form-group">
                        <label>公司別</label>
                        <select class="form-control" id="companyNo" name="companyNo">
                            @foreach(config('order.CompanyNoStrAry') as $k => $t)
                                @if(strlen($k) <= 3 && intval($k) < 999)
                                <option value="{{$k}}">{{ $k.' | '.$t }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1">
                        <label>訂編</label>
                        <input type="text" class="form-control" maxlength="8" id="subsid" name="subsid">
                    </div>
                    <div class="form-group mb-1">
                        <label>類別</label>
                        <input type="text" class="form-control" id="queryType" name="queryType">
                    </div>
                    <div class="form-group mb-1">
                        <label>問題說明</label>
                        <input type="text" class="form-control" maxlength="20" id="queryDesc" name="queryDesc">
                    </div>
                    <div class="form-group mb-1">
                        <label>解決方法</label>
                        <input type="text" class="form-control" maxlength="50" id="answer" name="answer">
                    </div>
                    <div class="form-group mb-1">
                        <label class="btn btn-info mb-0">
                            <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                            </svg>
                            <input class="d-none" type="file" accept="image/*" id="file" name="file" >
                            附件照片
                        </label>
                        <br>
                        <img src="" id="fileImg" width="300" onerror="this.hide;">
                        <hr>
                    </div>
                    <div class="form-group mb-1 d-flex float-right">
                        <button type="button" class="btn btn-primary" onclick="add()">送出</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- LoadIng --}}
    <div class="modal fade" id="loadIngQaMang" role="dialog" >
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

        // 選單資料
        var optionData = [];

        $(document).ready(function () {
            // $('#loadIngQaMang').modal('show');
            getList();
            $('#item').change(function(){
               let id = $(this).find(':selected').data('id');
               if(id < 1) {
                   $('form input').val('');
                   $('#companyNo option').eq(0).prop('selected',true);
                   $('#fileImg').attr('src','');
               } else {
                   let data = optionData[id];
                   $('#companyNo option[value="'+data.companyNo+'"]').prop('selected',true)
                   $('#id').val(data.Id);
                   $('#subsid').val(data.subsid);
                   $('#queryType').val(data.queryType);
                   $('#queryDesc').val(data.queryDesc);
                   $('#answer').val(data.answer);
                   $('#fileImg').attr('src',data.file);
               }
            });

            $('#file').change(function(){
                var input = $(this)[0];
                var file = input.files[0];
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function(e){
                    $('#fileImg').attr('src', e.target.result);
                }
            })
        });
        /*********** Redy end *************/

        // 新增[帖子]
        function add() {
            let url = '/api/EWO/apiOpinion';
            let files = $('#file')[0].files;
            if(
                $('#queryType').val() == '' ||
                $('#subsid').val() == '' ||
                $('#queryDesc').val() == '' ||
                $('#answer').val() == ''
            ) {
                alert('請填寫欄位');
                return;
            }

            if(files.length > 0){ console.log('ok107;');}
            var formData = new FormData();
            formData.append("func", 'add');
            formData.append("id", $('#id').val());
            formData.append("userCode", $('#userCode').val());
            formData.append("userName", $('#userName').val());
            formData.append("companyNo", $('#companyNo').val());
            formData.append("queryType", $('#queryType').val());
            formData.append("subsid", $('#subsid').val());
            formData.append("queryDesc", $('#queryDesc').val());
            formData.append("answer", $('#answer').val());
            formData.append("file", files[0]);
            $.ajax({
                url: url,
                method: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (result) {
                    console.log(result)
                    if(result.code == '0000') {
                        alert('送出成功')
                    } else {
                        alert('送出失敗;'+result.data+';'+result.code)
                    }
                    location.reload();
                    //
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //
                }
            });
        }


        // 取得[帖子]
        function getList() {
            let url = '/api/EWO/apiOpinion';
            var formData = new FormData();
            formData.append("func", 'getList');
            formData.append("userCode", $('#userCode').val());
            $.ajax({
                url: url,
                method: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    (result.data).forEach(function(t){
                        optionData[t.Id] = t;
                        $('#item').append(`<option data-id="${t.Id}">${t.Id}修改：${t.queryType}</option>`);
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //
                }
            });
        }

    </script>
@endsection
