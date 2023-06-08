@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main style="">
    <div class="container bg-grey">
        <input type="hidden" id="p_userCode" value="{{ $p_data['userCode'] }}">
        <input type="hidden" id="p_userName" value="{{ $p_data['userName'] }}">
        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    勞安設備照片
                </div>
            </div>
            <div class="card-body">
                <div class="input-group ">
                    <div class="input-group-prepend p-0 col-3">
                        <span class="input-group-text w-100 pl-1">登入帳號</span>
                    </div>
                    <div class="input-group-append p-0 col-9">
                    <span class="input-group-text bg-white w-100">
                        {{ $p_data['userCode'] }}
                    </span>
                    </div>
                </div>
                <div class="input-group ">
                    <div class="input-group-prepend p-0 col-3">
                        <span class="input-group-text w-100 pl-1">名稱</span>
                    </div>
                    <div class="input-group-append p-0 col-9">
                    <span class="input-group-text bg-white w-100">
                        {{ $p_data['userName'] }}
                    </span>
                    </div>
                </div>
                <div class="input-group ">
                    <div class="input-group-prepend p-0 col-3">
                        <span class="input-group-text w-100 pl-1">參考照片</span>
                    </div>
                    <div class="input-group-append p-0 col-9">
                        <img src="{{ asset('img/lsImgSampe.jpg') }}" width="100%">
                    </div>
                </div>
            </div>
        </div>

        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    <label class="btn btn-info mb-0">
                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                        </svg>
                        <input class="d-none" type="file" accept="image/*" id="lsImg">
                        勞安設備照片
                    </label>

                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" id="label_lsImg">
                        請上傳勞工安全設備照片。
                    </label>
                </div>
            </div>
            <div class="card-body col-3">
                <img class="" width="500" id="img_lsImg"
                     src="/ewo_lsImg/{{ date('Ym') }}/{{ date('Ymd') }}/lsImg_{{ date('Ymd') }}_{{ $p_data['userCode'] }}.jpg?i={{date('His')}}"
                     onerror="this.src='/img/error_02.png'">
            </div>
        </div>
    </div>

</main>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {

        $('#lsImg').change(function () {


            var uploadImg = new UploadImg();
            uploadImg.addFileAndSend(this);

            function UploadImg() {
                var xhr = new XMLHttpRequest();
                const LENGTH = 1024 * 1024 * 0.3; //0.5M
                var start = 0;
                var end = start + LENGTH;
                var blob;
                var blob_num = 1;
                var is_stop = 0;

                this.addFileAndSend = function (that) {
                    var file = that.files[0];
                    blob = cutFile(file);
                    sendFile(blob, file);
                    blob_num += 1;
                };


                function cutFile(file) {
                    var file_blob = file.slice(start, end);
                    start = end;
                    end = start + LENGTH;
                    return file_blob;
                }

                function sendFile(blob, file) {
                    var total_blob_num = Math.ceil(file.size / LENGTH);
                    var fileName = 'lsImg.jpg';
                    var columnName = 'lsImg';

                    var formData = new FormData();
                    formData.append("image", blob);
                    formData.append("blob_num", blob_num);
                    formData.append("total_blob_num", total_blob_num);
                    formData.append('_token', '{{csrf_token()}}');
                    formData.append('fileName', fileName);
                    formData.append('id', $('#p_id').val());
                    formData.append('p_userCode', $('#p_userCode').val());
                    formData.append('p_userName', $('#p_userName').val());
                    formData.append('p_CustID', $('#p_custId').val());
                    formData.append('p_BookDate', $('#p_BookDate').val());
                    formData.append('p_CompanyNo', $('#p_companyNo').val());
                    formData.append('p_WorkSheet', $('#p_workSheet').val());
                    formData.append('p_columnName', columnName);

                    xhr.open("POST", "/ewo/order_info/uploadimg", false);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // console.log(xhr.responseText);
                            var json = JSON.parse(xhr.responseText);
                            // console.log(json);
                            $('#label_lsImg').html(json.meg+'#'+(json.date).substring(11,99));
                            if(json.data !== 'uploading') {
                                if((json.data.src).length > 0) {
                                    $('#img_lsImg').css('display','block');
                                    $('#img_lsImg').attr('src',json.data.src);

                                    $('#soSelect').prop('disabled',false);
                                    $('button[name="headerBtn"]').prop('disabled',false);
                                }
                            }

                        } //end success

                        var t = setTimeout(function () {
                            if (start < file.size && is_stop === 0) {
                                blob = cutFile(file);
                                sendFile(blob, file);
                                blob_num += 1;
                            } else {
                                setTimeout(t);
                            }
                        }, 1000);
                    };
                    xhr.send(formData);
                }
            }

        });

    });
    /*********** Redy end *************/

</script>
@endsection
