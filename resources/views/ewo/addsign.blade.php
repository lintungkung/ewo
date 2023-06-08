
<script src="/js/signature/js/signature_pad.umd.min.js"></script>

<div class="container bg-grey collapse" id="addSign" name="divpage" >
    <input type="hidden" name="p_token" id="p_token" value="{{ csrf_token() }}">
    <input type="hidden" name="p_userCode" id="p_userCode" value="{{$p_data['userId']}}">
    <input type="hidden" name="p_userName" id="p_userName" value="{{$p_data['userName']}}">
    <input type="hidden" name="p_sign_chs" id="p_sign_chs">

    <div class="alert alert-primary collapse" role="alert" id="addSign_alert">
        補簽名清單，查詢中
    </div>

    @foreach($p_data['addsign'] as $k => $t)
    <div class="card mb-3 " id="{{ "card_".$t->WorkSheet }}">
        <div class="card-header pt-0 pb-0">
            <input type="hidden" id="p_id_{{ $t->WorkSheet }}" value="{{ $t->Id }}">

            <input type="hidden" id="p_pdfv_{{ $t->WorkSheet }}" value="{{ $t->pdf_v }}">
            <input type="hidden" id="p_BookDate_{{ $t->WorkSheet }}" value="{{ $t->BookDate }}">
            <input type="hidden" id="p_custId_{{ $t->WorkSheet }}" value="{{ $t->CustID }}">
            <input type="hidden" id="p_companyNo_{{ $t->WorkSheet }}" value="{{ $t->CompanyNo }}">
            <input type="hidden" id="p_workSheet_{{ $t->WorkSheet }}" value="{{ $t->WorkSheet }}">
            <input type="hidden" id="p_forder_{{ $t->WorkSheet }}" value="{{ $t->forder }}">


            <button class="btn btn-success btn-open m-0" onclick="openBtn('{{ $t->WorkSheet }}')" id="btnOpen_{{ $t->WorkSheet }}">
                <svg  width="25" height="25" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
                </svg>
            </button>
            <button class="btn btn-outline-info btn-close m-0" onclick="closeBtn('{{ $t->WorkSheet }}')" id="btnClose_{{ $t->WorkSheet }}" style="display: none">
                <svg  width="25" height="25" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                </svg>
            </button>
            <label >
                (補)簽名 {{ $t->CompanyNo.'-'.$t->WorkSheet }}
            </label>
            {{--Detail--}}
            <div class="input-group mb-3">
                <div class="input-group-prepend p-0 col-3">
                    <span class="input-group-text w-100">服務別</span>
                </div>
                <div class="input-group-append input-group-text p-0 col-9 bg-white w-100">
                    {{ $t->servicenamelist }}
                </div>
                <div class="input-group-prepend p-0 col-3">
                    <span class="input-group-text w-100">用戶</span>
                </div>
                <div class="input-group-append input-group-text p-0 col-9 bg-white w-100">
                    {{ $t->CustName }}
                </div>
                <div class="input-group-prepend p-0 col-3">
                    <span class="input-group-text w-100">電話</span>
                </div>
                <div class="input-group-append input-group-text p-0 col-9 bg-white w-100">
                    {{ $t->phonelist }}
                </div>
                <div class="input-group-prepend p-0 col-3">
                    <span class="input-group-text w-100">地址</span>
                </div>
                <div class="input-group-append input-group-text p-0 col-9 bg-white w-100">
                    {{ $t->InstAddrName }}
                </div>
            </div>

        </div>
        <div class="card-body" style="display: none;">
            @if(strpos($t->servicenamelist,'CATV') !== false || strpos($t->servicenamelist,'DSTB') !== false)
            {{--DSTB sign--}}
            <div class="input-group sign-group" id="signDiv_dstb_{{ $t->WorkSheet }}">
                <div class="input-group-prepend alert alert-primary col-12 " role="alert">
                    DSTB 簽名
                </div>
                <div class="input-group-prepend p-0" id="signButton_dstb_{{ $t->WorkSheet }}">
                    <button class="btn btn-success mr-3" id="signRestBtn_dstb_{{ $t->WorkSheet }}" onclick="resetSignButton('open','dstb','{{ $t->WorkSheet }}')">重新簽名</button>
                    <button class="btn btn-info mr-3 d-none" id="signUpBtn_dstb_{{ $t->WorkSheet }}" onclick="signUpload('dstb','{{ $t->WorkSheet }}');resetSignButton('close','dstb','{{ $t->WorkSheet }}')">上傳</button>
                    <button class="btn btn-secondary d-none" id="signCloseBtn_dstb_{{ $t->WorkSheet }}" onclick="resetSignButton('close','dstb','{{ $t->WorkSheet }}')">取消</button>
                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_dstb">
                        DSTB 簽名
                    </label>
                </div>
                <img src="/upload/{{ $t->forder }}/sign_dstb_{{ $t->WorkSheet }}.jpg" width="500" id="signShow_dstb_{{ $t->WorkSheet }}">
                <div id="signaturePad_dstb_{{ $t->WorkSheet }}" class="signature-pad d-none">
                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                        <canvas id="upSignImg_dstb_{{ $t->WorkSheet }}"></canvas>
                    </div>
                    <div class="signature-pad--footer">
                        <div class="signature-pad--actions">
                            <div>
                                <button type="button" id="signClear_dstb_{{ $t->WorkSheet }}" class="button clear" data-action="clear">重寫</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(strpos($t->servicenamelist,'CM') !== false)
            {{--CM sign--}}
            <div class="input-group sign-group" id="signDiv_cm_{{ $t->WorkSheet }}">
                <div class="input-group-prepend alert alert-primary col-12 " role="alert">
                    CM 簽名
                </div>
                <div class="input-group-prepend p-0" id="signButton_cm_{{ $t->WorkSheet }}">
                    <button class="btn btn-success mr-3" id="signRestBtn_cm_{{ $t->WorkSheet }}" onclick="resetSignButton('open','cm','{{ $t->WorkSheet }}')">重新簽名</button>
                    <button class="btn btn-info mr-3 d-none" id="signUpBtn_cm_{{ $t->WorkSheet }}" onclick="signUpload('cm','{{ $t->WorkSheet }}');resetSignButton('close','cm','{{ $t->WorkSheet }}')">上傳</button>
                    <button class="btn btn-secondary d-none" id="signCloseBtn_cm_{{ $t->WorkSheet }}" onclick="resetSignButton('close','cm','{{ $t->WorkSheet }}')">取消</button>
                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_cm_{{ $t->WorkSheet }}">
                        CM 簽名
                    </label>
                </div>
                <img src="/upload/{{ $t->forder }}/sign_cm_{{ $t->WorkSheet }}.jpg" width="500" id="signShow_cm_{{ $t->WorkSheet }}">
                <div id="signaturePad_cm_{{ $t->WorkSheet }}" class="signature-pad d-none">
                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                        <canvas id="upSignImg_cm_{{ $t->WorkSheet }}"></canvas>
                    </div>
                    <div class="signature-pad--footer">
                        <div class="signature-pad--actions">
                            <div>
                                <button type="button" id="signClear_cm_{{ $t->WorkSheet }}" class="button clear" data-action="clear">重寫</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(strpos($t->servicenamelist,'TWMBB') !== false)
            {{--TWMBB sign--}}
            <div class="input-group sign-group" id="signDiv_twmbb_{{ $t->WorkSheet }}">
                <div class="input-group-prepend alert alert-primary col-12 " role="alert">
                    TWMBB 簽名
                </div>
                <div class="input-group-prepend p-0" id="signButton_twmbb_{{ $t->WorkSheet }}">
                    <button class="btn btn-success mr-3" id="signRestBtn_twmbb_{{ $t->WorkSheet }}" onclick="resetSignButton('open','twmbb','{{ $t->WorkSheet }}')">重新簽名</button>
                    <button class="btn btn-info mr-3 d-none" id="signUpBtn_twmbb_{{ $t->WorkSheet }}" onclick="signUpload('twmbb','{{ $t->WorkSheet }}');resetSignButton('close','twmbb','{{ $t->WorkSheet }}')">上傳</button>
                    <button class="btn btn-secondary d-none" id="signCloseBtn_twmbb_{{ $t->WorkSheet }}" onclick="resetSignButton('close','twmbb','{{ $t->WorkSheet }}')">取消</button>
                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_twmbb_{{ $t->WorkSheet }}">
                        TWMBB 簽名
                    </label>
                </div>
                <img src="/upload/{{ $t->forder }}/sign_twmbb_{{ $t->WorkSheet }}.jpg" width="500" id="signShow_twmbb_{{ $t->WorkSheet }}">
                <div id="signaturePad_twmbb_{{ $t->WorkSheet }}" class="signature-pad d-none">
                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                        <canvas id="upSignImg_twmbb_{{ $t->WorkSheet }}"></canvas>
                    </div>
                    <div class="signature-pad--footer">
                        <div class="signature-pad--actions">
                            <div>
                                <button type="button" id="signClear_twmbb_{{ $t->WorkSheet }}" class="button clear" data-action="clear">重寫</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="input-group sign-group" >
                <button class="btn btn-danger w-100" onclick="createPDF('{{ $t->WorkSheet }}')">PDF</button>
                <div id="pdf_show_{{ $t->WorkSheet }}" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<script>

    $(document).ready(function () {
        //
        // console.log('page addsign ready')

    });
    /*********** Redy end *************/


    function openBtn(id) {
        $('#addSign .card').hide();
        $('#addSign .btn-open').hide();
        $('#addSign .btn-close').hide();
        $('#card_'+id).show();
        $('#card_'+id+' .card-body').show();
        $('#btnClose_'+id).show();
    }

    function closeBtn(id) {
        $('#addSign .card').show();
        $('#addSign .btn-open').show();
        $('#addSign .btn-close').hide();
        $('#addSign .card-body').hide();
        $('#card_'+id).show();
    }



    // 簽名檔，上傳
    function signUpload(servName,id) {
        const canvas = document.getElementById("upSignImg_"+servName+'_'+id);
        const dataURL = canvas.toDataURL('image/jpg')
        const blobBin = atob(dataURL.split(',')[1])
        const array = []
        for (let i = 0; i < blobBin.length; i++) {
            array.push(blobBin.charCodeAt(i))
        }
        const obj = new Blob([new Uint8Array(array)], { type: 'image/jpg' })

        //function upload(imgId,fileName,obj='') {

        var fileName = 'sign_'+servName+'_'+id+'.jpg';
        var p_columnName = 'sign_'+servName;
        var imgId = 'upSignImg_'+servName;

        var formData = new FormData();
        formData.append('image',obj);
        formData.append('_token',$('#p_token').val());
        formData.append('id',$('#p_id_'+id).val());
        formData.append('p_BookDate',$('#p_BookDate_'+id).val());
        formData.append('p_CustID',$('#p_custId_'+id).val());
        formData.append('p_CompanyNo',$('#p_companyNo_'+id).val());
        formData.append('p_WorkSheet',$('#p_workSheet_'+id).val());
        formData.append('p_userCode',$('#p_userCode').val());
        formData.append('p_userName',$('#p_userName').val());
        formData.append('blob_num','1');
        formData.append('total_blob_num','1');
        formData.append('fileName',fileName);
        formData.append('p_columnName',p_columnName);
        formData.append('p_sign_chs',$('#p_sign_chs').val());

        $("#" + imgId + "_img").remove();
        var url = '/ewo/order_info/uploadimg';
        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            // dataType:'json',
            success: function (json) {
                if(json['code'] !== "0000") {
                    alert('上傳圖片錯誤');
                    return false;
                }
                $('#signShow_'+servName+'_'+id).attr('src',json['data']);
                updatePDF(id);// 更新PDF

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('上傳檔案失敗[API Error!]');
                console.log(thrownError);
            }
        });
    }


    // 創建，簽名板
    function createSign(servName,id)
    {
        var idStr = 'upSignImg_'+servName+'_'+id;
        console.log('func_createSign_id==' +idStr);

        var canvas = document.getElementById(idStr);
        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            dotSize: 1, //點的大小
            minWidth: 5, //最細的線條寬度
            // maxWidth: 5, //最粗的線條寬度
        });

        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            console.log('ratio='+ratio)
            console.log('offsetWidth='+canvas.offsetWidth);
            console.log('offsetHeight='+canvas.offsetHeight);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            canvas.lineWidth = 111;
            signaturePad.clear();
        }
        window.onresize = resizeCanvas;
        resizeCanvas();
        $('#signClear_'+servName+'_'+id).click(function(){
            signaturePad.clear();
        });
        resetSignButton('close',servName+'_'+id)

    }


    // 重新簽名，Button
    function resetSignButton(p_type,servName,id) {
        if(p_type === 'open') {
            // 重新簽名
            $('#signRestBtn_'+servName+'_'+id).addClass('d-none');
            $('#signCloseBtn_'+servName+'_'+id).removeClass('d-none');
            $('#signUpBtn_'+servName+'_'+id).removeClass('d-none');
            $('#signShow_'+servName+'_'+id).addClass('d-none');
            $('#signaturePad_'+servName+'_'+id).removeClass('d-none');
            createSign(servName,id);
        } else if(p_type === 'close') {
            // 上傳/取消
            $('#signRestBtn_'+servName+'_'+id).removeClass('d-none');
            $('#signCloseBtn_'+servName+'_'+id).addClass('d-none');
            $('#signUpBtn_'+servName+'_'+id).addClass('d-none');
            $('#signShow_'+servName+'_'+id).removeClass('d-none');
            $('#signaturePad_'+servName+'_'+id).addClass('d-none');
        }

    }



    // update PDF
    function updatePDF(id) {
        var url = '/api/updatepdfinfo/'+$('#p_pdfv_'+id).val()+'/'+$('#p_id_'+id).val();
        $.ajax({
            url: url,
            type: 'get',
            cache: false,
            processData: false,
            contentType: false,
            success: function (json) {
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
            }
        });
    }




    // 建立PDF
    function createPDF(id) {
        $('#pdfBody').find('div object').remove();
        var url = '/api/createpdf/app/'+$('#p_pdfv_'+id).val()+'/'+$('#p_id_'+id).val();
        var formData = new FormData();
        //console.log('chk url=='+url)
        $.ajax({
            url: url,
            type: 'get',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (json) {

                if(true) {
                    var p_forder = $('#p_forder_'+id).val();
                    var options = {
                        forcePDFJS: true,
                        fallbackLink: "<p>使用的瀏覽器或視窗格式不支援PDF預覽，請直接<a href=\"https://sms.hmps.cc/hr/HomeplusHR.pdf\">下載</a>觀看</p>",
                        PDFJS_URL: "{{ asset('/PDF_JS/web/viewer.html') }}"
                    };
                    PDFObject.embed("/upload/"+p_forder+'/'+id+'.pdf?_'+'{{ date('His') }}', "#pdf_show_"+id, options);
                }

                pdf_reload();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
            }
        });
    }


    // PDF reload
    function pdf_reload()
    {
        var iframeobj = $('#pdf_show').find('iframe')
        //iframeobj.attr('src')
        var timeSec = new Date().getSeconds() + new Date().getMilliseconds()
        var url = iframeobj.attr('src') + timeSec;
        iframeobj.attr('src',url)
        console.log(iframeobj.attr('src'));
    }

</script>
