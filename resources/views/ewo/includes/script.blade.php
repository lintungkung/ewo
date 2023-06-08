<script type="text/javascript">
    $(document).ready(function () {

        // 調整圖片，100%
        // $('img').width($(window).width() - 50)
        $('img').attr('width','90%')

        // 勞安-檢點表-個人防護檢點，撿查
        laborsafety_checklist_first_check();

        // get msg 一分鐘一次
        // setInterval(function () {
        //     console.log(new Date());
        //     getPushMSG();
        // }, 60000);

        $('#laborsafety_checklist_body').click(function(){
            laborsafety_checklist_first_check();
        });

        // 信用卡，輸入
        $("input[name='creditcardCode']").change(function(){
            $('#creditcardCode').val($('#creditcardCode1').val()+$('#creditcardCode2').val()+$('#creditcardCode3').val()+$('#creditcardCode4').val());
        });


        // 紙本工單
        $('#PaperPDF').click(function(){
            var objChk = $(this).prop('checked');
            var p_value = '{{date('Y-m-d H:i:s')}}';
            var params = {
                p_columnName : "PaperPDF",
                p_companyNo : $('#p_companyNo').val(),
                p_workSheet : $('#p_workSheet').val(),
                p_value : p_value,
                p_id : $('#p_id').val()
            }
            apiEvent('PaperPDF',params);
        });


        // 同戶欠費
        $('#arrears_btn').click(function(){
            let url = '/api/EWO/getSameHouseholdArrears';
            let data = [];
            data['custId'] = $('#p_custId').val();
            data['companyNo'] = $('#p_companyNo').val();
            data['workSheet'] = $('#p_workSheet').val();
            data['userCode'] = $('#p_userCode').val();
            data['userName'] = $('#p_userName').val();
            let dataJson = JSON.stringify(Object.assign({}, data));
            ajaxObj(url,dataJson,'arrearsAPISuccess');
        });


        // 順推-加購wifiAP
        defaultSaleAP('{{ $p_data['saleAP'] }}');

        $("input[type='file']").change(function() {
            var imgId = $(this).attr('id');
            var idAry = ['file_id_01','file_id_02','file_id03Photo','file_certificate_01','file_certificate_02','file_constructionPhoto','file_checkin']
                ,fNameAry = ['identity_01.jpg','identity_02.jpg','file_id03Photo','certificate_01.jpg','certificate_02.jpg','file_constructionPhoto','checkIn.jpg']
                ,p_imgIdIndex = idAry.indexOf(imgId);

            // 清單內的ID才處理，其他的挑過
            if(p_imgIdIndex < 0) return false;

            // 打卡，GPS
            if(imgId === 'file_checkin') {
                getLocalGPS();

                var prarams = {
                    p_columnName:(imgId.split('_')[1]),
                    p_id:$('#p_id').val(),
                    lat:$('#localLat').val(),
                    lng:$('#localLng').val(),
                }
                apiEvent('checkin', prarams);
            }

            var uploadImg = new UploadImg();
            uploadImg.addFileAndSend(this);

            function UploadImg() {
                var xhr = new XMLHttpRequest();
                // var formData = new FormData();
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

                // this.stop = function () {
                //     xhr.abort();
                //     is_stop = 1;
                // };

                function cutFile(file) {
                    var file_blob = file.slice(start, end);
                    start = end;
                    end = start + LENGTH;
                    return file_blob;
                }

                function sendFile(blob, file) {
                    var total_blob_num = Math.ceil(file.size / LENGTH);
                    var fileName = fNameAry[p_imgIdIndex];
                    if(imgId === 'file_constructionPhoto')
                        fileName = constructionPhotoUpload(imgId);
                    if(imgId === 'file_id03Photo')
                        fileName = id03PhotoUpload(imgId);

                    var formData = new FormData();
                    formData.append("image", blob);
                    formData.append("blob_num", blob_num);
                    formData.append("total_blob_num", total_blob_num);
                    formData.append('_token','{{csrf_token()}}');
                    formData.append('fileName',fileName);
                    formData.append('id',$('#p_id').val());
                    formData.append('p_userCode',$('#p_userCode').val());
                    formData.append('p_userName',$('#p_userName').val());
                    formData.append('p_CustID',$('#p_custId').val());
                    formData.append('p_BookDate',$('#p_BookDate').val());
                    formData.append('p_CompanyNo',$('#p_companyNo').val());
                    formData.append('p_WorkSheet',$('#p_workSheet').val());
                    formData.append('p_columnName', (imgId.split('_')[1]));

                    if(imgId === 'file_constructionPhoto') { // 施工照片
                        formData.append('names', $('#'+imgId).data('names'));

                    } else if(imgId === "file_checkin") { // 打卡
                        formData.append('lat',$('#localLat').val());
                        formData.append('lng',$('#localLng').val());
                        formData.append('custGps',$('#p_custGps').val());

                    } else if(imgId === "file_id03Photo") { // 第二證件
                        formData.append('names', $('#'+imgId).data('names'));

                    } else if(['upSignImg_cm','upSignImg_dstb','upSignImg_twmbb'].indexOf(imgId) >= 0) { // 簽名，加入 PDF版本
                        formData.append('p_columnName', 'sign_'+imgId.split('_')[1]);
                        formData.append('p_pdf_v', $('#p_pdf_v').val());

                    } else if(['file_id_01','file_id_02'].indexOf(imgId) >= 0) { // ID01、ID02
                        formData.append('p_columnName', imgId.split('_')[1] + imgId.split('_')[2]);

                    } else if(['file_certificate_01','file_certificate_02'].indexOf(imgId) >= 0) { // 憑證01、憑證02
                        formData.append('p_columnName', 'cert' + imgId.split('_')[2]);

                    }

                    //簽名檔名加[worksheet]
                    if(['sign_dstb.jpg', 'sign_cm.jpg', 'sign_twmbb.jpg','sign_mcust.jpg','sign_mengineer.jpg'].indexOf(fileName) >= 0) {
                        var fileNameAry = fileName.split('.');
                        fileName = fileNameAry[0]+'_'+$('#p_workSheet').val()+'.jpg';
                        formData.append('fileName',fileName);
                    }

                    xhr.open("POST", "/ewo/order_info/uploadimg", false);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            console.log(xhr.responseText);
                            var json = JSON.parse(xhr.responseText);
                            console.log(json);

                            if(json['code'] !== "0000") {
                                alert('上傳圖片錯誤');
                                return false;
                            }

                            if(['upSignImg','upSignImg_cm','upSignImg_dstb','upSignImg_twmbb','upSignImg_mcust','upSignImg_mengineer'].indexOf(imgId) >= 0) { // 簽名檔
                                var id_lastName = '_'+imgId.split('_')[1];
                                $('#signShow'+id_lastName).attr('src',json['data']['src']);
                                updatePDF();// 更新PDF

                            } else if(['file_id_01','file_id_02'].indexOf(imgId) >= 0) { // ID01、ID02
                                var imgIdAry = imgId.split('_');
                                var imgid = $('#img_' + imgIdAry[1] + '_' + imgIdAry[2]);
                                var labelid = $('#label_' + imgIdAry[1] + '_' + imgIdAry[2]);
                                labelid.removeClass('d-none');
                                if(json['data'] === 'uploading') {
                                    console.log(imgId+'：'+json['meg'])
                                    labelid.text(json['meg']);
                                } else {
                                    $('#alert_id_'+imgIdAry[2]).collapse('show');
                                    imgid.removeClass('d-none');
                                    imgid.attr('src', json.data.src);
                                    labelid.text('上傳時間：' + json.date);
                                    labelid.removeClass('d-none');

                                    // 證件照，圖片掃描
                                    idPhotoDialog(imgId,json.data.src,json.data.orc);
                                }

                                let p_id = fileName.split('.')[0];
                                console.log(fileName,p_id)
                                // load OK 再加[浮水印]
                                $('#' + p_id).on('load', function () {
                                    createImgWatemark(p_id);
                                })

                            } else if(imgId === 'file_constructionPhoto') {
                                // 施工照片
                                if(json['data'] === 'uploading') {
                                    console.log(imgId+'：'+json['meg'])
                                    $('#label_constructionPhoto').removeClass('d-none');
                                    $('#label_constructionPhoto').text(json['meg']);
                                } else {
                                    $('#label_constructionPhoto').text(json['date']);
                                    $('#file_constructionPhoto').data('names',json['data']['names']);
                                    var html_img = '<img class=" constructionPhoto-img pb-1 ml-1" width="'+$('#constructionPhoto_img').width() +'" ' +
                                        'name="'+fileName.split('.')[0]+'" src="'+json['data']['img']+'" ondblclick="constructionPhotoDBClick($(this))" />';
                                    var img_length = $('#constructionPhoto_img').find('img').length;
                                    if(img_length >= 5) {
                                        $('#constructionPhoto_img').find('img[name='+fileName.split('.')[0]+']').before(html_img);
                                        $('#constructionPhoto_img').find('img[name='+fileName.split('.')[0]+']').eq(1).remove();
                                    } else if(img_length < 1) {
                                        $('#constructionPhoto_img').html(html_img);
                                    } else {
                                        $('#constructionPhoto_img').find('img').eq(img_length-1).after(html_img);
                                    }
                                }

                            } else if(imgId === 'file_id03Photo') {
                                // 第二證件
                                var imgIdAry = imgId.split('_');
                                // var imgid = $('#img_' + imgIdAry[1]);
                                var labelid = $('#label_id03Photo');
                                if(json['data'] === 'uploading') {
                                    console.log(imgId+'：'+json['meg'])
                                    labelid.text(json['meg']);
                                    labelid.removeClass('d-none')
                                } else {
                                    labelid.text(json['date']);
                                    // 證件照，圖片掃描
                                    idPhotoDialog(imgId,json.data.src,json.data.orc);
                                }
                                $('#file_id03Photo').data('names', json['data']['names']);
                                var p_id = fileName.split('.')[0];
                                var html_img = '<div class="divWatemark mb-1">' +
                                    '<img class=" constructionPhoto-img" width="500" id="' + p_id + '" name="img_id03Photo" src="' + json['data']['img'] + '" ondblclick="id03PhotDBClick($(this))" />' +
                                    '</div>';
                                var img_length = $('#id03Photo_img').find('img').length;
                                if (img_length >= 3) {
                                    $('#id03Photo_img').find("div[class='divWatemark mb-1']").eq(0).after(html_img);
                                    $('#id03Photo_img').find("div[class='divWatemark mb-1']").eq(0).remove();
                                } else if (img_length < 1) {
                                    $('#id03Photo_img').html(html_img);
                                } else {
                                    $('#id03Photo_img').find("div[class='divWatemark mb-1']").eq(img_length - 1).after(html_img);
                                }

                                // 圖片重整
                                var d = new Date();
                                $('#id03Photo_img img').each(function(){
                                    var src = $(this).prop('src')
                                    $(this).attr('src',src + d.getTime())
                                })

                                // load OK 再加[浮水印]
                                $('#' + p_id).on('load', function () {
                                    createImgWatemark(p_id)
                                })

                            } else if(['file_certificate_01','file_certificate_02'].indexOf(imgId) >= 0) { // 憑證01、憑證02
                                var imgIdAry = imgId.split('_');
                                var imgid = $('#img_' + imgIdAry[1]+'_'+imgIdAry[2]);
                                var labelid = $('#label_' + imgIdAry[1]+'_'+imgIdAry[2]);
                                labelid.removeClass('d-none');
                                if(json['data'] === 'uploading') {
                                    console.log(imgId+'：'+json['meg'])
                                    labelid.text(json['meg']);
                                } else {
                                    imgid.removeClass('d-none');
                                    imgid.attr('src', json.data.src);
                                    labelid.text('上傳時間：' + json.date);
                                    labelid.removeClass('d-none');
                                }
                            } else if(['file_checkin'].indexOf(imgId) >= 0) {
                                // 打卡
                                var imgIdAry = imgId.split('_');
                                var imgid = $('#img_' + imgIdAry[1]);
                                var labelid = $('#label_' + imgIdAry[1]);
                                labelid.removeClass('d-none');
                                if(json['data'] === 'uploading') {
                                    console.log(imgId+'：'+json['meg'])
                                    labelid.text(json['meg']);
                                } else {
                                    imgid.removeClass('d-none');
                                    imgid.attr('src', json.data.src);
                                    labelid.text('上傳時間：' + json.date);
                                    labelid.removeClass('d-none');
                                    let gpsData = json['data']['checkInData']
                                    // $('#custGps').text(gpsData['custGps']);
                                    // $('#custAddres').text(gpsData['custAddres']);
                                    $('#checkInGPS').text(gpsData['checkInGps']);
                                    $('#checkInAddres').text(gpsData['checkInAddres']);
                                    $('#gpsDistance').text(gpsData['gpsDistance']);
                                }


                            } else {
                                var imgIdAry = imgId.split('_');
                                var imgid = $('#img_' + imgIdAry[1]);
                                var labelid = $('#label_' + imgIdAry[1]);
                                labelid.removeClass('d-none');
                                if(json['data'] === 'uploading') {
                                    console.log(imgId+'：'+json['meg'])
                                    labelid.text(json['meg']);
                                } else {
                                    imgid.removeClass('d-none');
                                    imgid.attr('src', json.data.src);
                                    labelid.text('上傳時間：' + json.date);
                                    labelid.removeClass('d-none');
                                }

                            }
                        } //end success

                        // var progress;
                        // // var progressObj = document.getElementById("finish");
                        // if (total_blob_num == 1) {
                        //     progress = "100%";
                        // } else {
                        //     progress =
                        //         Math.min(
                        //             100,
                        //             (blob_num / total_blob_num) * 100
                        //         ) + "%";
                        // }
                        // // progressObj.style.width = progress;
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
            } //end functoin

        });


        // 完工，檢查[完工]時間紀錄[關閉完工，刷卡功能]
        if(10) {
            var finshtimeCheck = '{{$p_data['info']->SheetStatus}}';
            if(finshtimeCheck == '4.結款') {
                $('#receivemoneyDiv').hide();
                $('#creditcardInputGroup').hide();
                $('#creditcardInputGroup').hide();
                $('#finshBtn').remove();
                $('#installFinshalert').hide();
            } else {
                $('#finshtimeAlert').hide();
                $('#creditcardInputGroup').hide();
            }
        }

        // wifi環境檢測
        if(10) {
            $('#WifiTestValue_btn').click(function(){
                wifitest_saveApi();
            });
        }

        //退單
        if(10) {
            $('#button_chargeback').click(function(){
               var p_backdesc = $('#chargeBackDesc').val();
               if(confirm(p_backdesc + '確認退單!!')) {
                   stbApi('{{config('order.CahrgeBackType')[$p_data['info']->WorkKind]}}_退單')
                   $('#button_chargeback').parent('label').addClass('d-none');
                   $('#label_chargeback').removeClass('d-none');
                   $('#label_chargeback').text('退單處理中...');
               }
            });
        }

        // 順推加購
        if(10) {
            $('#chargeProduct_Btn').click(function(){
                var p_product = $('#chargeProduct_select').val();
                if(p_product === "0") {
                    alert('請選擇加購產品')
                } else {
                    p_product = $('#chargeProduct_select :selected').text();
                    if(confirm('確認加購['+p_product+']?'))
                        stbApi('chargeproduct');
                }
            });
        }


        // wifi測試數據；手動輸入判斷
        $('input[name="wifiTest_value[]"]').change(function(){
            var p_id = $(this).attr('id');
            var p_sort = p_id.split('_');
            var p_grade = '';
            var wifi_val = parseFloat($(this).val());
            if(wifi_val > -40) {
                p_grade = '極佳';
            } else if(wifi_val <= -40 && wifi_val >= -55) {
                p_grade = '尚可';
            } else if(wifi_val < -55) {
                p_grade = '微弱';
            }
            $('#wifiTest_'+p_sort[1]+'_grade').val(p_grade);
            $('#wifiTest_'+p_sort[1]+'_grade').next().text(p_grade);
        });


        // 已核個資
        $('#certified_btn').click(function(){
            var p_val = $('#certified').val();
            if(confirm('個資['+p_val+']\n確認送出?')) {
                certified_saveApi()
            }
        });


        // 順推-加購wifiAP
        $('#saleap_btn').click(function(){
            let p_val = $("input[name='saleap']:checked").val();

            if(p_val === undefined) {
                alert('請先選擇[加購wifiAP]結果!');
                return ;
            }else if (p_val == '訊號不良，順推單購') {
                let workKind = $('#p_workkind').val();
                let serviceNameAry2 = $('#p_serviceNameAry2').val();
                if(['1 裝機','5 維修'].indexOf(workKind) < 0) {
                    saleAP_saveApi();
                    return ;
                }

                let url = '{{ config('order.R1_URL') }}/wifiMesh/order';
                let temp_form = document.createElement("form");
                temp_form.action = url;
                temp_form.target = "_self";
                temp_form.method = "post";
                temp_form.style.display = "none";

                let params = {
                    p_CustName : "{{ $p_data['info']->CustName }}",
                    p_CustID : "{{ $p_data['info']->CustID }}",
                    p_CompanyNo : "{{ $p_data['info']->CompanyNo }}",
                    p_CellPhone01 : "{{ $p_data['info']->CellPhone01 }}",
                    p_SaleCampaign : "{{ $p_data['info']->SaleCampaign }}",
                    p_WorkerName : "{{ $p_data['info']->WorkerName }}",
                    p_WorkerNum : "{{ $p_data['info']->WorkerNum }}",
                    p_WorkKind : "{{ $p_data['info']->WorkKind }}",
                    p_WorkTeam : "{{ $p_data['info']->WorkTeam }}",
                    p_WorkSheet : "{{ $p_data['info']->WorkSheet }}",
                    p_BookDate : "{{ $p_data['info']->BookDate }}",
                    p_ServiceName : "{{ $p_data['info']->ServiceName }}",
                    p_assignSheetAryJson : "{{ $p_data['assignSheetAryJson'] }}",
                    p_source: 'ewo_app',
                    backurl: window.location.href,
                };

                // 處理需要傳遞的參數
                for (let x in params) {
                    let opt = document.createElement("textarea");
                    opt.name = x;
                    opt.value = params[x];
                    temp_form.appendChild(opt);
                }
                document.body.appendChild(temp_form);
                // 提交表單
                temp_form.submit();
            } else {
                saleAP_saveApi();
            }

        });


        // 網路品質查詢
        $('input[name="cmqualityforkg_btn"]').click(function(){
            stbApi('cmqualityforkg',$(this).data('subsid'));
            $('#cmqualityforkg_'+$(this).data('subsid')+'_label').text('網路品質查找中...')
            alert('網路品質查找中')
        });


        // CM MAC連線資訊
        $('input[name="cmmacinfo_btn"]').click(function(){
            stbApi('cmmacinfo',$(this).data('subsid'));
            $('#cmmacinfo_'+$(this).data('subsid')+'_label').text('CM MAC連線資訊查找中...')
            alert('CM MAC連線資訊查找中')
        });


        // FTTH設備資訊
        $('input[name="ftthDeviceInfo_btn"]').click(function(){
            stbApi('queryFTTH',$('#companyno').val());
            $('#cmqualityforkg_'+$(this).data('subsid')+'_label').text('設備資訊查找中...')
            alert('設備資訊查找中')
        });


        // 網路品質查詢_存檔
        $('input[name="cmqualityforkg_save_btn"]').click(function(){
            var p_val = $(this).data('cmqualityforkg');
            var p_subsid = $(this).data('subsid');
            var params = {
                p_companyNo : $('#p_companyNo').val(),
                p_workSheet : $('#p_workSheet').val(),
                p_custid : $('#p_custId').val(),
                p_columnName : "cmqualityforkg",
                p_subsid : p_subsid,
                p_value : p_val,
                p_id : $('#p_id').val(),
            };
            if(p_val == '') {
                alert('無數據，請查詢後存檔');
                return;
            }
            apiEvent('cmqualityforkg_save',params);
            $('#cmqualityforkg_'+$(this).data('subsid')+'_label').text('CM品質查詢，存檔中...')
        });


        // CM MAC連線資訊 存檔
        $('input[name="cmmacinfo_save_btn"]').click(function(){
            var p_val = $(this).data('cmmacinfo');
            var p_subsid = $(this).data('subsid');
            var params = {
                p_companyNo : $('#p_companyNo').val(),
                p_workSheet : $('#p_workSheet').val(),
                p_custid : $('#p_custId').val(),
                p_columnName : "cmmacinfo",
                p_subsid : p_subsid,
                p_value : p_val,
                p_id : $('#p_id').val(),
            };
            if(p_val == '') {
                alert('無數據，請先查詢後存檔');
                return;
            }
            apiEvent('cmmacinfo',params);
            $('#cmmacinfo_'+$(this).data('subsid')+'_label').text('CM MAC連線資訊，存檔中...')
        });


        // 換機、維修，完工前，DSTB，設備清單先選擇
        if(['4 結案','4.結款','A.取消'].includes($('#p_sheetStatus').val()) === false
            && ($('#p_ServiceName').val()).search('DSTB') >= 1) {

            // 停用1028
            // if(($('#p_workkindAryStr').val()).search('維修') >= 0
            //     || ($('#p_workkindAryStr').val()).search('換機') >= 0) {
            //     deviceListChk();
            // }
        }


        // 完工、取消，不能使用任何東西
        if(['4 結案','4.結款','A.取消'].includes($('#p_sheetStatus').val())) {
            $('input').attr('disabled',true);
            $('button').attr('disabled',true);
            $('select').attr('disabled',true);

            $('#btnBack').attr('disabled',false);

            let openDisabled = ['sentmailpdf_btn','sentmailpdf_vlaue'];
            openDisabled.forEach(function(k){
                $('#'+k).attr('disabled',false);
            })
        }


        // 縮放表格，上下箭頭
        $('.btn_collapsed').click(function(){
            chkLaborSafetyBtn($(this))
        });

        // 提示訊息，20221215
        if('{{ $p_data['info']->chkAlertMSG20221215 }}' == 'Y') {
            alert('注意：\n目前鋐寶CH8679因設備有問題，故暫停使用此設備供裝\n請改用其他設備供裝(如CODA5310)\n');
        }

    }); // end ready


    // 設備清單，確認
    function deviceListChk() {
        // 停用1028
        return '';
        if($('#equipmentListHead').data('count') == 1) {
            return '';
        }
        if($('#equipmentListHead').data('devchk') != '') {
            return '';
        }
        if(($('#p_ServiceName').val()).search('DSTB') <= 1) {
            return '';
        }
        location.href = '#equipmentListHead';
        $('#equipmentListBody').collapse('show');
        let p_workkind = $('#p_workkind').val();
        p_workkind = p_workkind.substring(2,99);
        alert('請先在設備清單選取['+p_workkind+']的對象!');
        return 'Y';
    }


    // 設備序號，輸入
    function setDevicSelign() {
        var singleSnAry = [];
        $('#inpDevSinBtnBody ul li.singlesn').each(function(){
            let ary = [];
            let inputObj = $(this).find('input[name="newSingleSn"]')
            let newSingleSN = inputObj.val();
            let model = $(this).find('select').val();
            let subsId = inputObj.data('subsid');
            let chargeName = inputObj.data('chargename');
            ary['subsId'] = subsId;
            ary['chargeName'] = chargeName;
            ary['singleSn'] = newSingleSN;
            ary['model'] = model;
            singleSnAry.push(Object.assign({}, ary))
        });
        let singleSnAryObj = Object.assign({}, singleSnAry);

        let url = '/api/EWO/setDeviceSingleSN';
        let data = [];
        data['userCode'] = $('#p_userCode').val();
        data['userName'] = $('#p_userName').val();
        data['companyNo'] = $('#p_companyNo').val();
        data['custId'] = $('#p_custId').val();
        data['workSheet'] = $('#p_workSheet').val();
        data['singleSnAry'] = singleSnAryObj;
        let dataJson = JSON.stringify(Object.assign({}, data));

        $.ajax({
            url: url,
            type: 'post',
            headers: {"Content-Type": "application/json"},
            data: dataJson,
            success: function (result) {
                $('#inpDevSinLab').text(result.date);
                if(result.code === '0000') {
                    Object.entries(result.data).forEach(entry => {
                        const [subsId, value] = entry;
                        let chargeName = value['chargeName'];
                        if(value['result'] === 'OK') {
                            $("li[name='subsId_chargeName_" + subsId + '#' + chargeName + "']").text('序號:' + value['singleSn']+'型號:' + value['model']);
                        }
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('設備序號送出失敗');
            }
        });
    }


    // ajax function
    function ajaxObj(url,params,funcName) {
        $.ajax({
            url: url,
            type: 'post',
            headers: {"Content-Type": "application/json"},
            data: params,
            success: function (result) {
                let myJsonString = JSON.stringify(result);
                var func = new Function('return '+funcName+"('" + myJsonString + "')");
                func();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('API錯誤[ajaxObj]');
            }
        });
    }


    // 同戶欠費，API，result
    function arrearsAPISuccess(result) {
        let resultAry = JSON.parse(result);
        if(resultAry.code === '0000') {
            $('#arrears_alert').text(resultAry.date);
            $('#arrears_table tbody tr').remove();
            let htmlStr = ``;
            let sumTotal = 0;
            Object.entries(resultAry.data).forEach(entry => {
                const [k, t] = entry;
                htmlStr = `
                    <tr>
                        <td>${t['No']}</td>
                        <td>${t['CompanyNo']}</td>
                        <td>${t['SubsID']}</td>
                        <td>${t['AssignSheet']}</td>
                        <td>${t['ServiceName']}</td>
                        <td>${t['ChargeName']}</td>
                        <td>${parseInt(t['billamt'])}</td>
                    </tr>
                    `;
                $('#arrears_table tbody').append(htmlStr);
                sumTotal += parseInt(t['billamt']);

            });

            htmlStr = `
                    <tr>
                        <td colspan="6">合計</td>
                        <td>${sumTotal}</td>
                    </tr>
                    `;
            $('#arrears_table tbody').append(htmlStr);
        }
    }


    // 檢點表，按鈕，切換圖案
    function chkLaborSafetyBtn(obj) {
        let btn01 = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
</svg>`;
        let btn02 = `<svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
</svg>`;
        let pid = obj.data('target');
        let classStr = $(''+pid).attr('class');
        let classAry = classStr.split(' ');
        let chkClass = classAry.includes('show');
        obj.find('svg').remove();
        let btnStr = obj.html();
        if(chkClass) {
            htmlStr = btnStr+btn02;
        } else {
            htmlStr = btnStr+btn01;
        }
        obj.html(htmlStr);
    }


    // 勞安-檢點表-存檔(鈕)檢查
    function laborsafety_checklist_first_check() {
        var chkLaborsafetyFrist = '';
        $('#laborsafety_checklist_body').find('ul').eq(0).each(function () {

            $(this).find('li.list-group-item.pt-0.pb-0').each(function () {
                var desc03 = $(this).find('input[type="checkbox"]').prop('checked');
                if(desc03 === false && chkLaborsafetyFrist === '') {
                    chkLaborsafetyFrist = 'Y';
                }

            });
        });

        if(chkLaborsafetyFrist === 'Y') {
            $('#laborsafety_checklist_save_btn').text('請先確認(個人防護檢點)');
            $('#laborsafety_checklist_save_btn').prop('disabled',true);
        } else {
            $('#laborsafety_checklist_save_btn').text('存檔');
            $('#laborsafety_checklist_save_btn').prop('disabled',false);
        }
    }


    // 勞安-危險地點
    function laborsafetyDialog() {
        var p_window_height = $(window).height();
        var p_window_width = $(window).width();
        $('#laborsafetyDialog').removeClass('d-none');
        $('#laborsafetyDialog').dialog({
            autoOpen: false,
            width: p_window_width,
            height: p_window_height,
            open: function() {
                // $('.ui-dialog-titlebar').css('display','none'); //toll display
                $('.ui-dialog-titlebar-close').hide(); // close ican
                $('.ui-dialog-content').css('height','auto'); //sign height
                $('.ui-dialog-content').css('padding','0'); //sign paddign
                $('.ui-dialog.ui-corner-all').css('height','100%'); //sign background height 100%
            },
        });
        $('#laborsafetyDialog').dialog("open");

        document.body.style.overflow = 'hidden';
    }


    // 證件照，圖片掃描
    function idPhotoDialog(f_id,src,orc) {
        var p_window_height = $(window).height();
        var p_window_width = $(window).width();
        var p_title = [];
        p_title['file_id_01'] = '身分證正面，圖片';
        p_title['file_id_02'] = '身分證反面，圖片';
        p_title['file_id03Photo'] = '第二證件，圖片';

        p_title['upSignImg'] = '簽名檢查';
        p_title['upSignImg_cm'] = 'CM簽名檢查';
        p_title['upSignImg_dstb'] = 'DSTB簽名檢查';
        p_title['upSignImg_twmbb'] = 'TWMBB簽名檢查';
        p_title['upSignImg_mcust'] = '用戶簽名檢查';
        p_title['upSignImg_mengineer'] = '工程簽名檢查';
        $('#idphoto_dialog img').attr('src',src);
        $('#idphoto_dialog label').html(orc);
        $('#idphoto_dialog .card-header').html(p_title[f_id]);

        $('#idphoto_dialog').removeClass('d-none');
        $('#idphoto_dialog').dialog({
            autoOpen: false,
            width: p_window_width,
            height: p_window_height,
            open: function() {
                // // $('.ui-dialog-titlebar').css('display','none'); //toll display
                $('.ui-dialog-titlebar-close').hide(); // close ican
                // $('.ui-dialog-content').css('height','auto'); //sign height
                // $('.ui-dialog-content').css('padding','0'); //sign paddign
                // $('.ui-dialog.ui-corner-all').css('height','100%'); //sign background height 100%
            },
        });
        $('#idphoto_dialog').dialog("open");

        //卷軸固定
        document.body.style.overflow = 'hidden';
    }


    // 危險地點(dialog)關閉
    function laborsafety_dangerplaceDialogClose(p_id='') {
        $('#laborsafetyDialog').dialog('close');

        document.body.style.overflow = 'scroll';

        var desc1 = $('#laborsafetyDialog').find('.card-header p').html();
        var desc2 = [];
        $('#laborsafetyDialog').find('.card-body li').each(function(){
            //console.log($(this).html())
            desc2.push($(this).html())
        })
        desc2 = JSON.stringify(desc2);

        var params = {
            p_columnName :  'laborsafety_dangerplace',
            p_companyNo :  $('#p_companyNo').val(),
            p_workSheet :  $('#p_workSheet').val(),
            p_custId :  $('#p_custId').val(),
            p_instAddr :  $('#p_instAddr').val(),
            p_bookdate : $('#p_BookDate').val(),
            p_id : p_id,
            p_type :  'B.危險地點',
            p_desc1 : desc1,
            p_desc2 : desc2,
            p_reply :  '確認',
            // EventType :  'laborsafetylog_B',
        };

        apiEvent('laborsafety_dangerplace',params);
    }




    // 提示dialog
    function alertDialog(servName) {
        var p_window_height = $(window).height();
        var p_window_width = $(window).width();
        $('#alertDialog').removeClass('d-none');
        $('#alertDialog').dialog({
            autoOpen: false,
            width: p_window_width,
            height: p_window_height,
            open: function() {
                // $('.ui-dialog-titlebar').css('display','none'); //toll display
                $('.ui-dialog-titlebar-close').hide(); // close ican
                $('.ui-dialog-content').css('height','auto'); //sign height
                $('.ui-dialog-content').css('padding','0'); //sign paddign
                $('.ui-dialog.ui-corner-all').css('height','100%'); //sign background height 100%
            },
        });

        // 提示，開啟
        $('#signRestBtn_'+servName).on("click", function () {
            document.body.style.overflow = 'hidden';
            // wifit test
            editDialogHtml_wifiteset();
            wifitest_saveApi();

            // sale wifiAP
            editDialogHtml_saleAP();
            saleAP_saveApi();

            // edit cust certified
            // editDialogHtml_certified();
            // certified_saveApi();

            $('#alertDialog').dialog("open");
        });

        // 提示，關閉
        $('#alertDialog').on('dialogclose',function(){
            document.body.style.overflow = 'scroll';
        });

    }

    // default value set radio checked
    function defaultSaleAP(p_chkVal = '') {
        ($("input[name='saleap']")).each(function(){
            var p_radio = $(this).val();
            {{--var p_chkVal = '{{ $p_data['saleAP'] }}';--}}
            if(p_radio == p_chkVal) {
                $(this).attr('checked',true);
                return ;
            }
        });
    }

    // edit html for dialog 加購wifiAP
    function editDialogHtml_saleAP(){
        $('#card_saleap').remove();

        var p_val = 'null';
        var p_checked = $('input[name="saleap"]:checked').length;
        if(p_checked > 0) {
            p_val = $('input[name="saleap"]:checked').val();
        } else {
            alert('請選擇[順推加購wifiAP]');
        }

        var htmlStr = '';
        htmlStr += '' +
            '<div class="card w-80" id="card_saleap">\n' +
            '    <div class="card-header">\n' +
            '        順推-加購wifiAP\n' +
            '    </div>\n' +
            '    <div class="card-body">\n' +
            '    <label>'+ p_val +'</label>' +
            '    </div>\n' +
            '</div>';

        $('#alertDialog_close_btn').before(htmlStr);

    }


    // success edit html wifitest
    function editDialogHtml_wifiteset(){
        $('#card_wifitest').remove();
        var htmlStr = '';
        htmlStr += '' +
            '<div class="card w-80 mt-3 mb-3" id="card_wifitest" >\n' +
            '    <div class="card-header">\n' +
            '        本次安裝wifi測試數據如下\n' +
            '    </div>\n' +
            '    <div class="card-body">\n' +
            '        <ul class="list-group list-group-flush">\n';
        $("input[name='wifiTest_value[]']").each(function(){
            var p_id = $(this).attr('id');
            var p_id_ary = p_id.split('_');
            var v1 = $('#wifiTestPoint_'+p_id_ary[1]+'_select').val();
            var v2 = $(this).val();
            var v3 = $('#wifiTest_'+p_id_ary[1]+'_grade').val();

            htmlStr += '' +
                '<li class="list-group-item">第('+(parseInt(p_id_ary[1]) + 1)+')常用地點=>'+v1+', 數值:'+v2+' ['+v3+']</li>\n';
        });
        htmlStr += '' +
            '        </ul>\n' +
            '    </div>\n' +
            '</div>';

        $('#alertDialog_close_btn').before(htmlStr);

    }

    // wifi-test salve
    function wifitest_saveApi() {
        var p_point, p_wifiTestvalue, p_grade, p_valueJson;
        var a = new Date()
        var b = a.toLocaleString("sv-SE",{timeZone:'Asia/Taipei'});
        var p_valAry = [];
        for(var i=0; i < 5; i++) {
            p_point = $('#wifiTestPoint_'+i+'_select').val();
            p_floor = $('#wifiTestFloor_'+i+'_select').val();
            p_wifiTestvalue = $('#wifiTest_'+i+'_value').val();
            p_grade = $('#wifiTest_'+i+'_grade').val();
            p_valAry[i] = {
                'floor':p_floor,
                'pont':p_point,
                'value':p_wifiTestvalue,
                'grade':p_grade
            };
        }
        p_valAry[5] = {
            'floor':'測試時間',
            'pont':'',
            'value':b,
            'grade':''
        };
        p_valueJson = JSON.stringify(p_valAry);

        var params = {
            p_companyNo : $('#p_companyNo').val(),
            p_workSheet : $('#p_workSheet').val(),
            p_columnName : "WifiTestValue",
            p_value : p_valueJson
        };

        apiEvent('WifiTestValue',params)
    }

    // 個人個資 save
    function certified_saveApi() {
        var p_val = $('#certified').val();
        var params = {
            p_companyNo : $('#p_companyNo').val(),
            p_workSheet : $('#p_workSheet').val(),
            p_columnName : "certified",
            p_value : p_val,
        };

        apiEvent('certified',params)
    }

    // 順推-加購wifiAP save
    function saleAP_saveApi() {
        var p_val = $("input[name='saleap']:checked").val();

        if(p_val === undefined) {
            return ;
        }

        var params = {
            p_companyNo : $('#p_companyNo').val(),
            p_workSheet : $('#p_workSheet').val(),
            p_custid : $('#p_custId').val(),
            p_columnName : "saleAP",
            p_value : p_val
        };
        apiEvent('saleap',params);
    }


    if(0)
    function signDialog(servName) {
        var p_window_height = $(window).height();
        var p_window_width = $(window).width();
        createSign(servName);
        $('#sign_'+servName+'_dialog').dialog({
            autoOpen: false,
            width: p_window_width,
            height: p_window_height,
            open: function() {
                $('.ui-dialog-titlebar-close').hide(); // close ican
                $('.ui-dialog-titlebar').css('display','none'); //toll display
                $('.ui-dialog-content').css('height','auto'); //sign height
                $('.ui-dialog-content').css('padding','0'); //sign paddign
                $('.ui-dialog.ui-corner-all').css('height','100%'); //sign background height 100%
            },
        });

        // 重新簽名
        $('#signRest_'+servName+'_btn').on("click", function () {
            document.body.style.overflow = 'hidden';
            $('#sign_'+servName+'_dialog').dialog("open");

            // test
            if('{{ $p_data['IsTest'] }}' > 0) {
                editHtml_wifiteset();
                wifitest_saveApi();
                $('#alertDialog').dialog("open");
            }
        });

        // 簽名關閉
        $('#sign_'+servName+"_dialog").on('dialogclose',function(){
            document.body.style.overflow = 'scroll';
            $('#signShow_'+servName+'').width($(window).width()-50);
        });

    }


    // 簽名檔，上傳
    function signUpload(servName) {
        const canvas = document.getElementById("upSignImg_"+servName);
        const dataURL = canvas.toDataURL('image/jpg')
        const blobBin = atob(dataURL.split(',')[1])
        const array = []
        for (let i = 0; i < blobBin.length; i++) {
            array.push(blobBin.charCodeAt(i))
        }
        const obj = new Blob([new Uint8Array(array)], { type: 'image/jpg' })

        //function upload(imgId,fileName,obj='') {

        var fileName = 'sign_'+servName+'_'+$('#p_workSheet').val()+'.jpg';
        var p_columnName = 'sign_'+servName;
        var imgId = 'upSignImg_'+servName;

        // var img_colum = [];
        // img_colum['sign.jpg'] = 'sign';
        // img_colum['sign_cm.jpg'] = 'sign_cm';
        // img_colum['sign_dstb.jpg'] = 'sign_dstb';
        // img_colum['sign_twmbb.jpg'] = 'sign_twmbb';
        // img_colum['sign_mcust.jpg'] = 'sign_mcust';
        // img_colum['sign_mengineer.jpg'] = 'sign_mengineer';

        var formData = new FormData();
        formData.append('image',obj);
        formData.append('_token',$('#p_token').val());
        formData.append('id',$('#p_id').val());
        formData.append('p_BookDate',$('#p_BookDate').val());
        formData.append('p_CustID',$('#p_custId').val());
        formData.append('p_CompanyNo',$('#p_companyNo').val());
        formData.append('p_WorkSheet',$('#p_workSheet').val());
        formData.append('p_userCode',$('#p_userCode').val());
        formData.append('p_userName',$('#p_userName').val());
        formData.append('blob_num','1');
        formData.append('total_blob_num','1');
        formData.append('fileName',fileName);
        formData.append('p_columnName',p_columnName);
        formData.append('p_sign_chs',$('#p_sign_chs').val());
        formData.append('p_sign_bftth',$('#p_sign_bftth').val());

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
                if(json['code'] == '0101') {
                    alert('上傳圖片異常，請重新上傳-1/2。');
                    alert('上傳圖片異常，請重新上傳-2/2。');
                }
                else if(json['code'] !== "0000") {
                    alert('上傳圖片錯誤');
                    return false;
                }
                var id_lastName = '_'+imgId.split('_')[1];
                $('#signShow'+id_lastName).attr('src',json['data']['src']);
                updatePDF();// 更新PDF

                var alertText = $('#signAlert_'+servName).text();
                if(alertText.search(';') > 0)
                    alertText = alertText.substr(0,alertText.search(';'));

                var alertStr = alertText;
                if(json['code'] == '0101') {
                    alertStr += ';上傳檔案異常，請重新上傳';
                    $('#signAlert_'+servName).removeClass('alert-info')
                    $('#signAlert_'+servName).addClass('alert-danger')
                }
                alertStr += ';上傳時間:'+json.date;
                $('#signAlert_'+servName).text(alertStr);

                idPhotoDialog(imgId,json.data.src);

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('上傳檔案失敗[API Error!]');
                console.log(thrownError);
            }
        });
    }


    // update PDF
    function updatePDF() {
        var url = '/api/updatepdfinfo/'+$('#p_pdf_v').val()+'/'+$('#p_id').val();
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


    function stbApi(apitype,params='') {
        var url = '{{config('order.STB_API')}}/api/';
        var jsonData = {
            'companyNo': $('#p_companyNo').val(),
            'workSheet': $('#p_workSheet').val(),
            'custId': $('#p_custId').val(),
            'p_userCode': $('#p_userCode').val(),
            'p_userName': $('#p_userName').val(),
            'worker':$('#p_userCode').val()
        };
        console.log('stbAPI jsonData==');
        console.log(jsonData);
        switch(apitype)
        {
            case "authorstb": //開通
                $('#openalert').text('等待結果');
                var obj = $('#open'+params+'_scanstr');
                var strVal = obj.val();
                var chkStr = strVal.search('Facisno')
                if(chkStr < 0) {
                    $('#openalert').text('資料有錯，請重新掃描');
                    alert('掃描資料有錯[not find Facisno]!')
                    return false;
                }

                // 新增紀錄
                addStbMAtvTestValueLog('open'+params+'_scanstr','stb_atvqrcode');

                var strAry = strVal.split(',')
                var deviceAry = strAry[1].split(':');
                var deviceName = deviceAry[0];
                var deviceNo = deviceAry[1];
                //var p_IncludeHD = 0;//固定0 2021/6/17
                var p_HDSerialNo = '';
                if(strVal.search('HDSerialNo')) {
                    //p_params = strVal.substr(strVal.search('HDSerialNo')+12,15)
                    var strValAry = strVal.split(',');
                    strValAry.forEach(function(t){
                        if(t.search('HDSerialNo') > 0) {
                            var HDSerialNoAry = t.split(':')
                            p_HDSerialNo = HDSerialNoAry[1].replace(' ','');
                        }
                    })

                    //p_IncludeHD = 1;
                }
                if(deviceName !== ' Facisno')
                {
                    $('#openalert').text('資料有錯，請重新掃描');
                    alert('開通資料有錯[not find Facisno]!')
                    return false;
                }
                jsonData['deviceNo'] = deviceNo;
                jsonData['mobile'] = $('#p_userMobile').val();
                jsonData['ivrNo'] = $('#open'+params+'_btn').data('subscp');
                jsonData['workSheet'] = params;
                //jsonData['IncludeHD'] = p_IncludeHD;//固定0 2021/6/17
                jsonData['HDSerialNo'] = p_HDSerialNo;
                jsonData['p_qrCode'] = strVal;

                url += 'authorstb';
                break;

            case "1_裝機":
            case "2_復機":
            case "6_移機":
            case "8_工程收費":
            case "9_停後復機":
            case "A_加裝":
            case "C_換機":
                url += 'installedfinished';
                var radioText = $('#receivemoney').find('option:selected').text();
                var p_receivemoney = $('#receivemoney').val();
                var chk = true;
                chk = confirm('確認：'+radioText)
                if(!chk){
                    alert('請確任收款方式!')
                    return;
                }
                if(p_receivemoney === '1') {//刷卡=1
                    if(creditcardNumCheck() == false){
                        console.log('信用卡檢查錯誤');
                        return false;
                    }
                    $('#creditcardAlert').text('刷卡處理中...');
                }
                if(p_receivemoney === '3') {//完工未收=1
                    p_receivemoney = 1;
                }

                // 主單單號已完工，促變單號未完工
                // var finshsheet = $('#p_finshSheet').val();
                // var worksheet = $('#p_workSheet').val();
                // if(finshsheet != worksheet) {
                //     jsonData['workSheet'] = finshsheet;
                // }

                jsonData['dataMatch'] = p_receivemoney;
                jsonData['p_receiveType'] = p_receivemoney;
                jsonData['p_receiveMoney'] = $('#p_recvAmt').val();
                jsonData['phoneNum'] = $('#p_phoneNum').val();
                jsonData['serviceName'] = $('#p_ServiceName').val();
                jsonData['p_id'] = $('#p_id').val();
                jsonData['p_subsidStr'] = $('#p_subsidStr').val();
                jsonData['p_pdf_v'] = $('#p_pdf_v').val();
                jsonData['p_worksheet2'] = $('#p_worksheet2').val();
                $('#installFinshalert').text('處理中...');
                $('#finshBtn').parent('label').hide();
                break;

            case "3_拆機":
            case "4_停機":
            case "7_移拆":
            case "H_退拆設備":
            case "I_退拆分機":
            case "K_退次週期項":
            case "U_到宅取設備":
                url += 'removefinished';
                var p_receivemoney = $('#receivemoney').val();
                var radioText = $('#receivemoney').find('option:selected').text();

                if(p_receivemoney === '1') {//刷卡=1
                    if(creditcardNumCheck() == false){
                        console.log('信用卡檢查錯誤');
                        return false;
                    }
                }

                chk = confirm('確認：'+radioText)
                if(!chk){
                    alert('請確任收款方式!')
                    return;
                }

                if(p_receivemoney === '3') {//完工未收=1
                    p_receivemoney = 1;
                }

                jsonData['devices'] = 'ms0200.SingleSN';

                if(apitype == 'U_到宅取設備') {
                    jsonData['workSheet'] = jsonData['workSheet'].substr(0,jsonData['workSheet'].length - 1);
                    jsonData['devices'] = $('#p_orgSingleSnList').val();
                }

                jsonData['dataMatch'] = p_receivemoney;
                jsonData['p_receiveType'] = p_receivemoney;
                jsonData['p_receiveMoney'] = $('#p_recvAmt').val();
                jsonData['phoneNum'] = $('#p_phoneNum').val();
                jsonData['p_id'] = $('#p_id').val();
                jsonData['p_subsidStr'] = $('#p_subsidStr').val();
                jsonData['p_pdf_v'] = $('#p_pdf_v').val();
                jsonData['p_worksheet2'] = $('#p_worksheet2').val();
                $('#installFinshalert').text('處理中...');
                $('#finshBtn').parent('label').hide();
                break;

            case "5_維修":
                url += 'maintainfinished';
                var p_receivemoney = $('#receivemoney').val();
                var radioText = $('#receivemoney').find('option:selected').text();
                var p_first = $('#select_srviceReasonFirst').val();
                var p_last = $('#select_srviceReasonLast').val();

                if(p_first === '請選擇維修原因') {
                    alert('請選擇維修原因[第一個]');
                    return 0;
                }

                if(p_last === '請選擇') {
                    alert('請選擇維修原因[第二個]');
                    return 0;
                }

                chk = confirm('確認：'+radioText)
                if(!chk){
                    alert('請確任收款方式!')
                    return;
                }

                if(p_receivemoney === '1') {//刷卡=1
                    if(creditcardNumCheck() == false){
                        console.log('信用卡檢查錯誤');
                        return false;
                    }
                    $('#creditcardAlert').text('刷卡處理中...');
                }

                if(p_receivemoney === '3') {//完工未收=1
                    p_receivemoney = 1;
                }

                jsonData['mfCode1'] = p_first;
                jsonData['mfCode2'] = p_last;
                jsonData['dataMatch'] = p_receivemoney;
                jsonData['p_receiveType'] = p_receivemoney;
                jsonData['p_receiveMoney'] = $('#p_recvAmt').val();
                jsonData['phoneNum'] = $('#p_phoneNum').val();
                jsonData['p_id'] = $('#p_id').val();
                jsonData['p_subsidStr'] = $('#p_subsidStr').val();
                jsonData['p_pdf_v'] = $('#p_pdf_v').val();
                jsonData['p_worksheet2'] = $('#p_worksheet2').val();
                $('#installFinshalert').text('處理中...');
                $('#finshBtn').parent('label').hide();
                break;

            case "4_退單":
            case "8_退單":
            case "11_退單":
                var p_desc = $('#chargeBackDesc :selected').val();
                var p_type = (apitype.split('_'))[0];
                jsonData['type'] = p_type;
                jsonData['returnCode'] = p_desc;
                url += 'chargeback';
                break;
            case "chgEquipment": //修改，維修設備
                url += 'changedevice';
                var val_subsid = $("input[name='chg_siginsn']:checked").data('si');
                var val_siginsn = $("input[name='chg_siginsn']:checked").data('sn');
                var val_smartcard = $("input[name='chg_siginsn']:checked").data('sc');
                jsonData['custid'] = "{{$p_data['info']->CustID}}";
                jsonData['subsid'] = val_subsid;
                jsonData['singleSN'] = val_siginsn;
                jsonData['smartCard'] = val_smartcard;
                break;
            case "serviceResonLast": // 維修原因[api回傳第二層]
                $('#select_srviceReasonLast').find('option').remove();
                if($('#select_srviceReasonFirst :selected').text() === '請選擇維修原因') {
                    return false;
                } else {
                    $('#select_srviceReasonLast').find('option').remove();
                    var p_html = '<option>資料下載中...</option>';
                    $('#select_srviceReasonLast').append(p_html);
                }
                url += 'servicesecondreason';
                var p_servicecode = $('#select_srviceReasonFirst :selected').data('servicecode');
                var p_mscode = $('#select_srviceReasonFirst :selected').data('mscode');
                jsonData['services'] = p_servicecode;
                jsonData['firstCode'] = p_mscode;
                break;
            case 'creditcard': //完工>>刷信用卡
                url += 'creditcard';
                jsonData['creditNumber'] = $('#creditcardCode').val();
                jsonData['validDate'] = $('#creditcardMMYY').val();
                jsonData['amount'] = $('#p_recvAmt').val();
                jsonData['assignSheet'] = '{{$p_data['info']->AssignSheet}}';
                jsonData['p_subsidStr'] = $('#p_subsidStr').val();
                jsonData['phoneNum'] = $('#p_phoneNum').val();
                $('#creditcardAlert').text('刷卡處理中...')
                $('#finshBtn').parent('label').hide();
                break;

            case 'rebootdstb': //重開DSTB
                url += 'rebootdstb';
                jsonData['smartcard'] = params;
                $('#rebootdstb_'+params+'_label').text('SmartCard:'+params+'重開DSTB處理中...')
                $('#rebootdstb_'+params+'_btn').addClass('d-none');
                break;

            case 'cmqualityforkg': // 網路品質查詢
                url += 'cmqualityforkg';
                jsonData['subsId'] = params;
                break;

            case 'queryFTTH': // FTTH設備資訊
                url = '{{config('order.STB_API')}}/api/EWO/queryFTTH';
                break;

            case 'costmodify': // 修改維修金額
                url += 'costmodify';
                jsonData['subsId'] = params;
                jsonData['custId'] = $('#p_custId').val();
                jsonData['cost'] = parseInt($('#costModify_value').val());
                $('#costModify_label').text('修改處理中...');
                break;

            case 'chargeproduct': // 加購產品
                url += 'chargeproduct';
                jsonData['custId'] = $('#p_custId').val();
                jsonData['product'] = $('#chargeProduct_select').val();
                $('#chargeProduct_label').text('加購處理中...');
                break;

            case 'sentmailpdf': // 寄送amil
                url += 'sentmailpdf';
                jsonData['mail'] = params;
                jsonData['bookdate'] = $('#p_BookDate').val();
                $('#sentmailpdf_label').text('寄送信件，處理中...');
                $('#sentmailpdf_btn').parents('label').addClass('d-none');
                break;

            case 'restcm': //RestCM，重置CM
                url += 'restcm';
                jsonData['subsId'] = params;
                $('#ResetCM_label').removeClass('d-none')
                $('#ResetCM_label').text('處理中...')
                break;
            // case 'restartcm': //RestCM，重開CM 2022-10-14停用
            //     url += 'restartcm';
            //     jsonData['subsId'] = params;
            //     $('#ReStartCM_label').removeClass('d-none')
            //     $('#ReStartCM_label').text('處理中...')
            //     break;
            case 'cmwifirestart': // CM-Wifi重開
                url += 'cmwifirestart';
                jsonData['subsId'] = params;
                jsonData['wifiType'] = $('#cmwifirestart_wifiType').val();
                $('#cmwifirestart_label').removeClass('d-none')
                $('#cmwifirestart_label').text('處理中...')
                break;
            case 'cmmacinfo': //RestCM，重置CM
                url += 'cmmacinfo';
                jsonData['subsId'] = params;
                $('#CM_MACInfo_label').removeClass('d-none')
                $('#CM_MACInfo_label').text('處理中...')
                break;
            case 'proceede015': // 頻道重新授權
                url += 'proceede015';
                jsonData['subsid'] = params['subsid'];
                jsonData['smartcard'] = params['smartcard'];
                break;
            default:
                alert('API 錯誤[error005]');
                return;
                break;
        }

        $.ajax({
            url: url,
            type: 'post',
            data: jsonData,
            cache: false,
            dataType:'json',
            success: function (json) {
                console.log('-------------STB API----------');
                console.log(json);
                if(apitype === "authorstb") // 開通
                {
                    openApiResponse(json,params);
                }
                else if(['4_退單','8_退單','11_退單'].indexOf(apitype) >= 0) // 退單
                {
                    $('#button_chargeback').parent('label').removeClass('d-none');
                    var p_str = '退單:'+json['meg']+'; '+json['date'];
                    $('#label_chargeback').text(p_str);
                }
                else if(apitype === "serviceResonLast") // 維修原因[第二層]
                {
                    if(json.code === '0000')
                        setServiceResonLast(json.data);
                    else
                        alert('維修原因[api錯誤]');
                }
                else if(apitype === "chgEquipment") // 換設備SN
                {
                    $('#label_chgSiginsn').removeClass('d-none');
                    var p_str = "切換singleSN:"+json['data']['singleSN']+';時間:'+json['date'];
                    $('#label_chgSiginsn').text(p_str);
                    $('#equipmentListHead').data('devchk','OK');
                }
                else if(['1_裝機','C_換機','2_復機','6_移機','8_工程收費','9_停後復機','A_加裝'].indexOf(apitype) >= 0) // 裝機完工
                {
                    $('#finshBtn').parent('label').show();
                    updatePDF();// 更新PDF
                    installFinshApiResponse(json);
                }
                else if(['3_拆機','4_停機','7_移拆','H_退拆設備','I_退拆分機','K_退次週期項'].indexOf(apitype) >= 0) // 拆機完工
                {
                    $('#finshBtn').parent('label').show();
                    updatePDF();// 更新PDF
                    installFinshApiResponse(json);
                }
                else if(['5_維修'].indexOf(apitype) >= 0) // 維修完工
                {
                    $('#finshBtn').parent('label').show();
                    installFinshApiResponse(json);
                }
                else if(apitype === 'creditcard') //完工>>刷信用卡
                {
                    $('#finshBtn').parent('label').show();
                    $('#creditcardAlert').text('刷卡，訊息：'+json.meg+'；狀態：'+json.status+'；代碼：'+json.code+'；時間：'+json.date)
                }
                else if(apitype === 'restcm') //RestCM，重置CM
                {
                    $('#ResetCM_label').text('重置CM '+json.meg+'；狀態:'+json.status+'；時間:'+json.date)
                }
                // else if(apitype === 'restartcm') //ReSartCM，重開CM 2022-10-14停用
                // {
                //     $('#ReStartCM_label').text('重開CM '+json.meg+'；狀態:'+json.status+'；時間:'+json.date)
                // }
                else if(apitype === 'cmwifirestart') // cmwifirestart，CM-Wifi重開
                {
                    $('#cmwifirestart_label').text('CM-Wifi重開 '+json.meg+'狀態:'+json.status+'；時間:'+json.date)
                }
                else if(apitype === 'cmmacinfo') // CM MAC 連線資訊
                {
                    if(json.code !== '0000') {
                        alert('CM MAC連線資訊[error_code:'+json.code+']')
                        console.log(json)
                        var labelStr = 'CM MAC連線資訊：'+json.meg+';Time:'+json.date;
                        $('#cmmacinfo_'+params+'_label').text(labelStr)
                        $('#cmmacinfo_'+params+'_save_btn').data('cmmacinfo','');
                        return;
                    }
                    var labelStr = 'CM MAC連線資訊：'+json.status+';Time:'+json.date;
                    $('#cmmacinfo_'+params+'_label').text(labelStr)
                    var p_str = JSON.stringify(json.data);
                    $('#cmmacinfo_'+params+'_save_btn').data('cmmacinfo',p_str);
                    var htmlUl = '<ul class="list-group list-group-flush">';
                    Object.entries(json.data).forEach((t,k) => {
                        htmlUl += `<li class="list-group-item">值：${t[0]}##${t[1]}</li>`;
                    });
                    htmlUl += '</ul>';
                    $('#cmmacinfo_'+params+'_Body').find('div').html('');
                    $('#cmmacinfo_'+params+'_Body').find('div').append(htmlUl);
                }
                else if(apitype === 'proceede015') // DSTB，頻道重新授權
                {
                    $('#label_channelAuthorization').removeClass('d-none');
                    $('#label_channelAuthorization').text('DSTB頻道重新授權； '+json.meg+'；狀態:'+json.status+'；時間:'+json.date)
                }
                else if(apitype === 'sentmailpdf') //寄送Mail[pdf]
                {
                    $('#sentmailpdf_label').text('郵件寄送；'+json.meg+'；狀態:'+json.status+'；時間:'+json.date);
                    $('#sentmailpdf_btn').parents('label').removeClass('d-none');
                }
                else if(apitype === 'costmodify') //修改維修金額
                {
                    $('#costModify_label').removeClass('d-none');
                    $('#costModify_label').text('修改維修金額: '+json.meg+'；狀態：'+json.status+'；時間：'+json.date);
                    if(json.code === '0000') {
                        alert('修改金額成功，需重新整理!!');
                        location.reload();
                    }
                }
                else if(apitype === 'chargeproduct') //加購產品
                {
                    $('#chargeProduct_label').removeClass('d-none');
                    $('#chargeProduct_label').text('加購產品: '+json.meg+'；狀態：'+json.status+'；時間：'+json.date);
                    if(json.code === '0000') {
                        alert('加購成功，需重新整理!!');
                        location.reload();
                    }
                }
                else if(apitype === 'rebootdstb') // 重置DSTB
                {
                    $('#rebootdstb_'+params+'_btn').removeClass('d-none');
                    $('#rebootdstb_'+params+'_label').text('SmartCard: '+params+'; 訊息:'+json.meg+'；狀態：'+json.status+'；時間：'+json.date);
                }
                else if(apitype === 'queryFTTH') // FTTH狀態查詢
                {
                    editHtml_ftthDeviceInfo(json)
                }
                else if(apitype === 'cmqualityforkg') // 網路品質查詢
                {
                    $('#addWorkerMain').removeClass('d-none'); // 轉[幹線]選擇

                    // 暫存hidden
                    $('#cmqualityforkg_'+params+'_value').val((json.source));
                    addStbMAtvTestValueLog('cmqualityforkg_'+params+'_value','querycminfo');

                    if(json.code !== '0000') {
                        alert('網路查詢品質[error_code:'+json.code+']')
                        console.log(json)
                        var labelStr = '網路查詢：'+json.meg+';Time:'+json.date;
                        $('#cmqualityforkg_'+params+'_label').text(labelStr);
                        $('#cmqualityforkg_'+params+'_save_btn').data('cmqualityforkg','');
                        return;
                    }
                    var labelStr = '網路查詢：'+json.status+';Time:'+json.date;
                    $('#cmqualityforkg_'+params+'_label').text(labelStr)
                    var p_str = JSON.stringify(json.data);
                    $('#cmqualityforkg_'+params+'_save_btn').data('cmqualityforkg',p_str);
                    var htmlUl = '<ul class="list-group list-group-flush">';
                    var d1 = json.data


                    var kAry = [];
                    kAry['DocsIfSigQSignalNoise'] = 'CM下行(RX)SNR(>=32)dB即時';
                    kAry['DocsIfDownChannelPower'] = 'CM下行(RX)接收功率(-10~15)dBmV即時';
                    kAry['DocsIfCmtsCmStatusSignalNoise'] = '上行(TX)SNR(>=30)dB即時';
                    kAry['DocsIfCmStatusTxPower'] = 'CM上行(TX)發射功率(40~52)dB即時';
                    kAry['true'] = '符合';
                    kAry['false'] = '不符合';
                    let bgColorAry = [];
                    bgColorAry['true'] = '';
                    bgColorAry['false'] = 'bg-danger';

                    for(var k in (d1)){
                        htmlUl += '<li class="list-group-item list-group-item-success">'+kAry[k]+'</li>';
                        var d2 = d1[k]
                        for(var k2 in d2) {
                            let d3 = d2[k2];
                            htmlUl += `<li class="list-group-item ${bgColorAry[d3['Qualified']]}">值：${d3['Value']}##${kAry[d3['Qualified']]}</li>`;
                        }
                    }
                    htmlUl += '</ul>';
                    $('#cmqualityforkg_'+params+'_Body').find('div').html('');
                    $('#cmqualityforkg_'+params+'_Body').find('div').append(htmlUl);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('API 失敗');
                $('#finshBtn').parent('label').show();
                $('#cmqualityforkg_'+params+'_label').text('網路品質查找失敗');
                console.log(xhr);
            }
        });
    }


    // ftth device info HTML edit
    function editHtml_ftthDeviceInfo(json) {
        $('#ftthDeviceInfo_label').html('查詢時間:'+json.date)
        let htmlStr = '';
        if(json.code == '0000') {

            htmlStr = `
                <ul class="list-group list-group-flush">
                    <li class="list-group-item list-group-item-info">
                        FTTH設備資訊
                    <\/li>
                `;
            Object.entries(json.data).forEach(entry => {
                let [k, t] = entry;
                htmlStr += `
                    <li class="list-group-item pt-0 pb-0">
                        <div class="input-group">
                            <span class="input-group-text">${k}<\/span>
                            <input type="text" class="form-control text-center bg-white" disabled value="${t}">
                        <\/div>
                    <\/li>`;
            });

            htmlStr += `<\/ul>`;


        } else {
            htmlStr = json.data;
        }
        $('#ftthDeviceInfo_Body .card-body').html(htmlStr);
    }


    // 施工照片，檔案名稱確認
    function constructionPhotoUpload(p_id)
    {
        var names = $('#'+p_id).data('names');
        var worksheet = '{{$p_data['info']->WorkSheet}}';
        var file_name = worksheet + '_construction_' ;
        if(names.indexOf(worksheet + '_construction_1.jpg') < 0)
            file_name += '1.jpg';
        else if(names.indexOf(worksheet + '_construction_2.jpg') < 0)
            file_name += '2.jpg';
        else if(names.indexOf(worksheet + '_construction_3.jpg') < 0)
            file_name += '3.jpg';
        else if(names.indexOf(worksheet + '_construction_4.jpg') < 0)
            file_name += '4.jpg';
        else if(names.indexOf(worksheet + '_construction_5.jpg') < 0)
            file_name += '5.jpg';
        else
            file_name += '1.jpg';

        // console.log('chk1850 file_name =='+file_name);
        //upload(p_id,file_name)
        return file_name;
    }


    // ID03[第二證件]，檔案名稱確認
    function id03PhotoUpload(p_id) {
        var names = $('#'+p_id).data('names');
        var worksheet = '{{$p_data['info']->WorkSheet}}';
        var file_name = worksheet + '_id03_' ;
        if(names.indexOf(worksheet + '_id03_1.jpg') < 0)
            file_name += '1.jpg';
        else if(names.indexOf(worksheet + '_id03_2.jpg') < 0)
            file_name += '2.jpg';
        else if(names.indexOf(worksheet + '_id03_3.jpg') < 0)
            file_name += '3.jpg';
            // else if(names.indexOf(worksheet + '_id03_4.jpg') < 0)
            //     file_name += '4.jpg';
            // else if(names.indexOf(worksheet + '_id03_5.jpg') < 0)
        //     file_name += '5.jpg';
        else
            file_name += '1.jpg';

        // console.log('chk1850 file_name =='+file_name);
        //upload(p_id,file_name)
        return file_name;
    }


    // 生成浮水印[DIV]
    function createImgWatemark(p_id) {
        var htmlStr = "<div class=\"imgWatemark\">限用申請中嘉服務限用申請中嘉服務限用申請中嘉服務</div>";
        var imgObj = $('#'+p_id)
            ,p_src = imgObj.attr('src')
            ,imgHeight = imgObj.height()
            ,imgWidth = imgObj.width()
            ,chkStr = imgObj.next().text()
            ,wmHeight = imgHeight / 2 + 10
            ,wmWidth = imgWidth / 2 - (179 / 2)
        ;

        if(p_src.search('error') < 1) {
            if (chkStr === '限用申請中嘉服務') {
                imgObj.next().remove();
            } else if (wmHeight > 0) {
                imgObj.after(htmlStr);
                imgObj.next().css('bottom', wmHeight)
                imgObj.next().css('left', 0)
            }
        }
    }


    // 檢查，信用卡卡號
    function creditcardNumCheck() {
        var chkVal = 0;
        var toDay = new Date(), chkDay;
        var creditcardCode = $('#creditcardCode').val();
        var creditcardMMYY = $('#creditcardMMYY').val();

        // 檢查卡號
        var re = /^\d{4}\d{4}\d{4}\d{4}$/;
        if (re.test(creditcardCode))
            chkVal += 1;
        // 檢查有效期
        re = /^\d{2}\/\d{2}$/;
        if (re.test(creditcardMMYY)) {
            chkVal += 2;
            var mm = creditcardMMYY.substr(0,2);
            var YY = creditcardMMYY.substr(3,2);
            var afterDay = new Date('20'+YY,mm-1,1)
            if(mm < 1 || mm > 12) {
                chkVal = 4;
            } else if (afterDay < toDay) {
                chkVal = 4;
            }
        }

        var error_msg = '';
        switch(chkVal) {
            case 0: // 資料都錯
                error_msg = '信用卡號，';
                break;
            case 1: // 有效期錯誤
                error_msg = '有效期，';
                break;
            case 2: // 信用卡號錯
                error_msg = '信用卡號，';
                break;
            case 4:
                error_msg = '有效期不正確，';
                break;
            case 3: // 資料正確
                //stbApi('creditcard');
                return true;
                break;
        }

        alert(error_msg+ '資料錯誤');
        if(chkVal == 2) {
            $('#creditcardCode1').focus();
        }
        else if(chkVal == 0) {
            $('#creditcardCode1').focus();
        }
        else {
            $('#creditcardMMYY').focus();
        }
        return false;
    }


    //勞安-檢點表，按鈕(全選)
    function btnChecklistAll(obj) {
        var val = obj.data('checked');
        var dataStr = val === 'true'? 'false' : 'true';
        var setVal = dataStr === 'true'? false : true;
        var htmlStr = dataStr === 'true'? '(全選)' : '(取消)';
        obj.html(htmlStr);
        obj.data('checked',dataStr);
        obj.parents('.list-group').find('input[type="checkbox"]').prop('checked',setVal)
    }

    //勞安-檢點表，存檔BTN
    function laborsafetyCheckListSave() {
        $('#laborsafety_checklist_save_btn').prop('disabled',true);
        $('#laborsafety_checklist_save_btn').text('存檔中...');

        var bookdate = $('#p_BookDate').val();
        var checklist = [];
        $('#laborsafety_checklist_body').find('ul').each(function(){
            var desc01 = $(this).find('.list-group-item.active.m-0').find('p').eq(0).text();
            //console.log(list01)

            $(this).find('li.list-group-item.pt-0.pb-0').each(function(){
                var desc02 = $(this).find('label').html();
                // console.log($(this).find('label').html());
                var desc03 = $(this).find('input[type="checkbox"]').prop('checked');
                var desc03_id = $(this).find('input[type="checkbox"]').data('id');
                // console.log($(this).find('input[type="checkbox"]').prop('checked'));

                var data = {
                    desc1 : desc01,
                    desc2 : desc02,
                    value : desc03,
                    bookdate : bookdate,
                    id : desc03_id
                };
                checklist.push(data);

            });
        });
        //console.log(checklist);
        var checklistJSON = JSON.stringify(checklist);

        var params = {
            _token : $('#p_token').val(),
            p_userCode : $('#p_userCode').val(),
            p_userName : $('#p_userName').val(),
            p_columnName : 'laborsafetyCheckList',
            p_companyNo : $('#p_companyNo').val(),
            p_workSheet : $('#p_workSheet').val(),
            p_custId : $('#p_custId').val(),
            p_type :  'A.檢點表',
            EventType :  'laborsafetyCheckList',
            checklist: checklistJSON
        };

        apiEvent('laborsafetyCheckList', params);

    }

    // CMNS測試，查詢
    function cmnsQuery() {
        $('#cmnsQueryBtn').prop('disabled',true);
        $('#cmnsQuery_label').text('查詢中...');
        let htmlStr = `border-danger
                    <tr colspan="2">
                        <td colspan>查詢中...</td>
                    </tr>`;
        $('#cmnsQueryBody table tbody tr').remove();
        $('#cmnsQueryBody table tbody').html(htmlStr);
        var params = {
            _token : $('#p_token').val(),
            p_userCode : $('#p_userCode').val(),
            p_userName : $('#p_userName').val(),
            p_columnName : 'cmnsQuery',
            p_companyNo : $('#p_companyNo').val(),
            p_custId : $('#p_custId').val(),
            p_workSheet : $('#p_workSheet').val(),
            p_custId : $('#p_custId').val(),
            EventType :  'cmnsQuery',
        };
        apiEvent('cmnsQuery', params);
    }


    // CMNS測試，存檔
    function cmnsQuerySave() {
        $('#cmnsQuerySaveBtn').prop('disabled',true);
        $('#cmnsQuery_label').html('存檔中...');
        var params = {
            _token : $('#p_token').val(),
            p_id : $('#p_id').val(),
            p_userCode : $('#p_userCode').val(),
            p_userName : $('#p_userName').val(),
            p_columnName : 'cmnsQuerySave',
            p_companyNo : $('#p_companyNo').val(),
            p_custId : $('#p_custId').val(),
            p_workSheet : $('#p_workSheet').val(),
            EventType :  'cmnsQuerySave',
            p_value :  $('#cmnsQuerySaveBtn').data('json'),
        };
        apiEvent('cmnsQuerySave', params);
    }


    // 完工檢核表，存檔
    function finishCheckListSave() {
        var tAry = [];
        var bAry = []
        var title = '';

        // 同步檢查簽名
        chkSign('chk_signShow_mcust');
        chkSign('chk_signShow_mengineer');

        if($('#finishChkListBody li input:checked').length != $('#finishChkListBody li input').length) {
            alert('請確認(完工，檢核表)全部清單是否勾選。');
            return;
        }
        $('#finishChkList_label').html('存檔中...');
        $('#finishChkListBody li input:checked').each(function(){
            let obj = $(this);
            title = obj.next('label').text();
            // title = title.replace('  ','');
            bAry = [];
            obj.parent('li').next().find('li').each(function(){
                // body = $(this).text();
                bAry.push($(this).text());
            });
            tAry.push({title:title,body:bAry})
        });
        var params = {
            _token : $('#p_token').val(),
            p_id : $('#p_id').val(),
            p_userCode : $('#p_userCode').val(),
            p_userName : $('#p_userName').val(),
            p_columnName : 'finishCheckList',
            p_companyNo : $('#p_companyNo').val(),
            p_custId : $('#p_custId').val(),
            p_workSheet : $('#p_workSheet').val(),
            EventType :  'finishCheckList',
            p_value :  JSON.stringify(tAry),
        };
        apiEvent('finishCheckList', params);
    }


    // api Event
    function apiEvent(type, params) {
        params['_token'] = $('#p_token').val();
        params['p_userCode'] = $('#p_userCode').val();
        params['p_userName'] = $('#p_userName').val();

        console.log('apiEvent==');
        console.log(params);

        $.ajax({
            method: 'POST',
            url: '/ewo/event',
            data: params,
            success: function (json) {
                console.log('apiEvent POST success;');
                console.log(json);
                if(json.code === "0000") {
                    switch(type) {
                        case 'finishCheckList' : // 完工檢核表
                            $('#finishChkListSaveBtn').prop('disabled',false);
                            $('#finishChkList_label').html('存檔OK;'+json.date);
                            $('#finishChkListSaveBtn').data('save','Y');
                            break;
                        case 'laborsafetyCheckList'://勞安-檢點表
                            var htmlStr = '存檔:'+json.date;
                            alert(htmlStr);
                            $('#laborsafety_checklist_alert').html(htmlStr);
                            $('#laborsafety_checklist_save_btn').prop('disabled',false);
                            $('#laborsafety_checklist_save_btn').text('存檔');
                            break;
                        case 'laborsafety_dangerplace'://勞安-危險地點
                            break;
                        case 'delatedesc': // 遲到原因
                            responseDelateDesc(json);
                            break;
                        case 'lineWill': // line 同意/不同意
                            var willArray = [];
                            willArray['Y'] = '同意';
                            willArray['N'] = '不同意';
                            $('#linkWill_alert').html(willArray[params.p_value]+';'+json.date);
                            break;
                        case 'SrviceReason': // 維修原因
                            responseSrviceReason(json);
                            break;
                        case 'constructionPhotoDel': // 施工照片，刪除
                            // console.log('constructionPhotoDel api success==');
                            // console.log(json)
                            // console.log(params)
                            var img_name = (params.fname).split('.')[0];
                            $('#constructionPhoto_img img[name="'+img_name+'"]').remove();
                            $('#file_constructionPhoto').data('names',json.data)
                            break;
                        case 'id03PhotoDel': // 第二證件，刪除
                            // console.log('constructionPhotoDel api success==');
                            // console.log(json)
                            // console.log(params)
                            var img_name = (params.fname).split('.')[0];
                            $('#id03Photo_img img[id="'+img_name+'"]').closest('div').remove();
                            $('#file_id03Photo').data('names',json.data)
                            break;
                        case 'hardConsSave': // 五金耗料，存檔
                            $('#hardConsLabel').removeClass('d-none')
                            $('#hardConsLabel').text('存檔時間:'+json.date);
                            break;
                        case 'sentmail': // PDF 寄送 mail
                            var obj = $('#sentmail').parents('.input-group');
                            obj.find('.input-group-append span').text(json.date);
                            break;
                        case 'PaperPDF': // 紙本工單
                            $('#'+type+'_alert').removeClass('d-none');
                            $('#'+type+'_alert').text(json.data);
                            break;
                        case 'checkin': // 打卡
                            $('#img_checkin').data('chk',2);

                            // 勞安，危險地點(打卡後出現)
                            var chkLaborsafety = '{{ count($p_data['laborsafety_dangerplace'])? 'Y': 'N' }}';
                            if(chkLaborsafety == 'Y') {
                                laborsafetyDialog();
                            }
                            break;
                        case 'serviceReasonRemarks': // 工程人員備註
                            console.log('serviceReasonRemarks==');
                            console.log(json);
                            $('#p_MS300MSremarkLabel').text(json.status+'; '+json.date);
                            $('#signShow_mcust_info01').text('工程人員備註：'+json.data);
                            $('#signShow_mcust_info01').removeClass('d-none');
                            break;
                        case 'demolitionflow': // 拆機流向
                            console.log('eventapi success demolitionflow==');
                            console.log(json);
                            $('#demolitionFlow_label').removeClass('d-none');
                            $('#demolitionFlow_label').text(json.msg+'; '+json.date);
                            break;
                        case 'certified': // 已核個資
                            // if(json.code === '0000')
                                $('#certified_label').text('送出'+json.msg+';'+json.date);
                            // else
                            //     $('#certified_label').text('送出'+json.msg+';'+json.date);
                            break;
                        case 'saleap': // 順推-加購wifiAP
                                $('#saleap_label').text('送出'+json.data+';'+json.date+';'+json.msg);
                            break;
                        case 'WifiTestValue': // wifi環境參數
                            if(json.code === '0000')
                                $('#WifiTestValue_label').text('存檔 成功; '+json.date)
                            else
                                $('#WifiTestValue_label').text('存檔 失敗，請重新送。'+json.data)
                            break;
                        case 'cmqualityforkg_save': // CM品質查詢，存檔
                            $('#cmqualityforkg_'+params.p_subsid+'_label').text('存檔 '+json.msg+' 成功; '+json.date)
                            break;
                        case 'BorrowmingList': // 設備-借用單
                        case 'RetrieveList': // 設備-取回單
                            break;
                        case 'termsPDFRead': // 條款讀取API，調整
                            console.log('apiEvent>>termsPDFRead')
                            console.log(params);
                            if(json.data['column'] == 'termsd')
                                $('#label_D_pdf').data('rtime',json.date);
                            else
                                $('#label_I_pdf').data('rtime',json.date);
                            chkPDFTreamRead(); // 判斷PDF閱讀，隱藏簽名欄位
                            break;
                        case 'cmnsQuery':
                            $('#cmnsQuery_label').html('查詢時間:'+json.date);
                            htmlStr = `
                                <tr>
                                    <td>測試時間</td>
                                    <td>${json.data.DateTime}</td>
                                </tr><tr>
                                    <td>CMMAC</td>
                                    <td>${json.data.CmMac}</td>
                                </tr><tr>
                                    <td>下載/上載速率</td>
                                    <td>${json.data.Rate}</td>
                                </tr><tr>
                                    <td>客戶實測值<br>(下載)</td>
                                    <td>${json.data.Download}</td>
                                </tr><tr>
                                    <td>客戶實測值<br>(上載)</td>
                                    <td>${json.data.Upload}</td>
                                </tr><tr>
                                    <td>Ping值(msec)</td>
                                    <td>${json.data.Ping}</td>
                                </tr><tr>
                                    <td>路由類別</td>
                                    <td>${json.data.Route}</td>
                                </tr><tr>
                                    <td>測速設備</td>
                                    <td>${json.data.UserDevice}</td>
                                </tr>
                            `;
                            $('#cmnsQueryBody').data('json',json.data);
                            $('#cmnsQueryBody table tbody tr').remove();
                            $('#cmnsQueryBody table tbody').html(htmlStr);
                            $('#cmnsQuerySaveDiv').removeClass('d-none');
                            $('#cmnsQueryBtn').prop('disabled',false);
                            $('#cmnsQuerySaveBtn').data('json',JSON.stringify(json.data));
                            break;
                        case 'cmnsQuerySave': // CMNS 存檔
                            $('#cmnsQuery_label').html('存檔完成;時間:'+json.date);
                            $('#cmnsQuerySaveBtn').prop('disabled',false);
                            break;
                        case 'cmmacinfo': // CM MAC連線資訊
                            $(`#cmmacinfo_${params.p_subsid}_label`).html('存檔完成;時間:'+json.date);
                            $(`#cmqualityforkg_${params.p_subsid}_save_btn`).prop('disabled',false);
                            break;
                        default:
                            console.log('default');
                            console.log(json);
                            break;
                    }
                } else {
                    // api error
                    switch(type) {
                        case 'finishCheckList' : // 完工檢核表
                            $('#finishChkListSaveBtn').prop('disabled',false);
                            $('#finishChkList_label').html('Error;'+json.data+'#'+json.date);
                            break;
                        case 'cmqualityforkg_save': // CM品質查詢，存檔 error
                            var p_subsid = params.p_subsid;
                                $('#cmqualityforkg_' + p_subsid + '_label').text('存檔失敗，請重新存檔。' + json.code + '#' + json.data)
                            break;
                        case 'cmnsQuery': // CMNS 測速查詢
                            htmlStr = `
                                <tr colspan="2">
                                    <td colspan>${json.data}</td>
                                </tr>
                            `;
                            $('#cmnsQueryBody table tbody tr').remove();
                            $('#cmnsQueryBody table tbody').html(htmlStr);
                            $('#cmnsQuery_label').html('查詢時間:'+json.date+';'+json.data);
                            $('#cmnsQuerySaveDiv').addClass('d-none');
                            $('#cmnsQueryBtn').prop('disabled',false);
                            break;
                        case 'cmnsQuerySave': // CMNS 存檔[失敗]
                            $('#cmnsQuerySaveBtn').prop('disabled',false);
                            $('#cmnsQuery_label').html('存檔失敗;'+'時間:'+json.date+';'+json.data);
                            break;
                        case 'cmmacinfo': // CM MAC連線資訊
                            $(`#cmmacinfo_${params.p_subsid}_label`).html('存檔失敗;時間:'+json.date);
                            $(`#cmqualityforkg_${params.p_subsid}_save_btn`).prop('disabled',false);
                            break;
                        default:
                            alert('失敗;'+json.code+'#'+json.data)
                            break;
                    }
                }
            }, error: function (xhr, ajaxOptions, thrownError) {
                alert('錯誤[code_'+type+']');

                switch(type) {
                    case 'laborsafety_checklist'://勞安-檢點表
                        $('#laborsafety_checklist_save_btn').prop('disabled', false);
                        $('#laborsafety_checklist_save_btn').text('存檔');
                        break;
                    case 'finishCheckList': // 完工檢核表
                        $('#finishChkListSaveBtn').prop('disabled',false);
                        $('#finishChkList_label').html('Error(api);');
                        break;
                    default:
                        alert('api error; Code:2069');
                        break;
                }

                console.log('錯誤[code:'+type+']');
                console.log(xhr);
                console.log(ajaxOptions);
                console.log(thrownError);
            }
        });
    }


    // 重新簽名，Button
    function resetSignButton(p_type,servName) {
        if(p_type === 'open') {
            // 重新簽名
            $('button#signRestBtn_'+servName).addClass('d-none')
            $('button#signCloseBtn_'+servName).removeClass('d-none')
            $('button#signUpBtn_'+servName).removeClass('d-none')
            $('#signShow_'+servName).addClass('d-none')
            $('#signaturePad_'+servName).removeClass('d-none')
        } else if(p_type === 'close') {
            // 上傳
            $('button#signRestBtn_'+servName).removeClass('d-none')
            $('button#signCloseBtn_'+servName).addClass('d-none')
            $('button#signUpBtn_'+servName).addClass('d-none')
            $('#signShow_'+servName).removeClass('d-none')
            $('#signaturePad_'+servName).addClass('d-none')
        }

    }


    // 創建，簽名板
    function createSign(servName) {
        var canvas = document.getElementById('upSignImg_'+servName);
        var signaturePad = new SignaturePad(canvas, {
            // backgroundColor: 'rgb(255, 255, 255)',
            backgroundColor: 'rgb(255, 255, 255)',
            dotSize: 1, //點的大小
            minWidth: 5, //最細的線條寬度
            // maxWidth: 5, //最粗的線條寬度
        });
        // var signaturePad02 = new SignaturePad(canvas, {
        //     backgroundColor: 'rgb(255, 255, 255)',
        //     dotSize: 1, //點的大小
        //     minWidth: 5, //最細的線條寬度
        //     // maxWidth: 5, //最粗的線條寬度
        // });

        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            canvas.lineWidth = 111;
            signaturePad.clear();
        }
        window.onresize = resizeCanvas;
        resizeCanvas();
        $('#signClear_'+servName).click(function(){
            $('#upSignImg_'+servName).data('clickchk','N');
            console.log('clear servName='+servName)
            console.log('clear='+$('#upSignImg_'+servName).data('clickchk'))
            signaturePad.clear();
        });

        // 粉紅畫面點選後
        // $('#upSignImg_'+servName).click(function(){
        //
        //     console.log('click servName='+servName)
        //     if($(this).data('clickchk') == 'N') {
        //         console.log('chk N')
        //         signaturePad02.clear();
        //     }
        //     console.log('click='+$(this).data('clickchk'))
        //     $(this).data('clickchk','Y');
        // });
        resetSignButton('close',servName)

        // test
        if($('#p_istest').val() > '0') {
            if($('#alertDialog').length > 0) {
                alertDialog(servName);
            }
        }
    }


    // 完工，API 裝機、維修、拆機 Response
    function installFinshApiResponse(jsonData) {
        console.log('installFinshApiResponse ===')
        console.log(jsonData)

        var chkVal = $('#receivemoney').val()
        if(chkVal === '1') //刷卡=1
            stbApi('creditcard'); // 開通結束，同時送[刷卡]

        //installFinshalert
        var strVal = "" +
            "訊息:" + jsonData['meg'] + "; " +
            "代號:" + jsonData['code'] + "; " +
            "狀態:" + jsonData['status'] + "; " +
            "時間:" + jsonData['date'] + "; "
        ;
        $('#installFinshalert').text(strVal);
        $('#finshAlert').text('完工API：OK'+jsonData['date']);
        //$('#finshButton').remove();

    }


    // 開通，API Response
    function openApiResponse(jsonData,worksheet) {
        //openalert
        var strVal = "" +
            "訊息:" + jsonData['meg'] + "; " +
            "代號:" + jsonData['code'] + "; " +
            "狀態:" + jsonData['status'] + "; " +
            "時間:" + jsonData['date'] + "; "
        ;
        console.log(jsonData)
        console.log(worksheet)
        $('#open'+worksheet+'_alert').text(strVal);
    }


    // 取得GPS經緯
    function getLocalGPS() {
        var locationValue;
        try {
            locationValue= app.getLocation();
        } catch (error) {
            locationValue = '111,222';
        }
        var location= locationValue.split(",");
        var latitude = location[0];
        var longitude= location[1];
        //alert(`${latitude},${longitude}`);
        $('#localLat').val(latitude);
        $('#localLng').val(longitude);
    }

    // 關閉，HDCP
    function disableHDCP()
    {
        var userid = $('#p_userCode').val();
        var so = $('#p_companyNo').val();
        var custid = $('#p_custId').val();
        var url = "{{config('order.R1_URL')}}"+'/api/chpAPI/v1/disableHDCP';
        $.ajax({
            url: url,
            type: 'post',
            data: JSON.stringify({
                uid: userid,
                so: so,
                custid: custid
            }),
            dataType: 'json',
            success:
                function(response){
                    var len = response.length;
                    if(len>0 && response=='OK')
                    {
                        alert('HDCP已關閉');
                    }
                    else
                        alert(response);
                }
        });
    }

    // 開啟，HDCP
    function enableHDCP()
    {
        var userid = $('#p_userCode').val();
        var so = $('#p_companyNo').val();
        var custid = $('#p_custId').val();
        var url = "{{config('order.R1_URL')}}"+"/api/chpAPI/v1/enableHDCP";
        $.ajax({
            url: url,
            type: 'post',
            data: JSON.stringify({
                uid: userid,
                so: so,
                custid: custid
            }),
            dataType: 'json',
            success:
                function(response){
                    var len = response.length;
                    if(len>0 && response=='OK')
                    {
                        alert('HDCP已打開');
                    }
                    else
                        alert(response);
                }
        });
    }


    // 取得 wifi測試數據
    function wifiTestFunc(p_id = '') {
        try {
            var wifi_val = app.getWifi();
            var p_select = $('#wifiTestPoint_'+p_id+'_select').val();
            var p_floor = $('#wifiTestFloor_'+p_id+'_select').val();
            var p_grade = '';
            $('#WifiTestValue_value').val(wifi_val);
            $('#wifiTest_'+p_id+'_value').val(wifi_val)

            $('#WifiTestValue_label').text('Wifi測試='+wifi_val);
            if(wifi_val > -40) {
                p_grade = '極佳';
            } else if(wifi_val > -40 && wifi_val < -55) {
                p_grade = '尚可';
            } else if(wifi_val < -55) {
                p_grade = '微弱';
            }
            $('#wifiTest_'+p_id+'_grade').val(p_grade);
            $('#wifiTest_'+p_id+'_grade').next().text(p_grade);

            alert('第'+parseInt(parseInt(p_id)+1)+'常上網:'+p_floor+p_select+' Wifi測試='+wifi_val+','+p_grade);

        } catch {
            $('#WifiTestValue_label').text('請下載安裝[App]')
            alert('請下載安裝[App]');

        }
    }

    // 檢查郵件格式
    function sentMailPdfChkMailValue(mail)
    {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
            stbApi('sentmailpdf', $('#sentmailpdf_vlaue').val())
        } else {
            $('#sentmailpdf_vlaue').val('');
            $('#sentmailpdf_label').text('Mail格式不合格!!');
        }

    }


    // 建立PDF
    function createPDF() {
        $('#pdfBody').find('div object').remove();
        var url = '/api/createpdf/app/'+$('#p_pdf_v').val()+'/'+$('#p_id').val()+'?cmd=Y';
        var formData = new FormData();
        $('#label_pdf').html('PDF生成中...');
        alert('PDF生成中...');
        //console.log('chk url=='+url)
        $.ajax({
            url: url,
            type: 'get',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (json) {

                if(json.code === '0000') {
                    var p_run = json.run;
                    var p_runAry =  JSON.parse(p_run);
                    var rtime = p_runAry['run_time'];

                    var htmlStr = '生成完成,R='+parseInt(rtime)+'秒;T='+json.date;
                    $('#label_pdf').html(htmlStr);
                }

                pdf_reload();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
            }
        });
    }

    function getPushMSG(){
        var method = "post";
        var path = "/api/getpushmsg";
        var params = {
            p_userCode : $('#p_userCode').val(),
            fromtype : 'app02'
        };

        $.ajax({
            method: method,
            url: path,
            data: params,
            success: function (json) {
                if(json.code !== '0000') {
                    return;
                }

                $('#msg_dialog_ul li').remove();
                $('#card_msg .card-header').text('查詢時間:'+json.date)

                var li_html = '';
                if(json.data.query.length > 0) {
                    (json.data.query).forEach(function (t) {
                        li_html += '<li class="list-group-item list-group-item-success">' + t.companyNo + '_' + t.workSheet + '_C:' + t.custId + '</li>';
                        li_html += '<li class="list-group-item list-group-item-info" data-id="' + t.Id + '">標題:' + t.title + '</li>';
                        li_html += '<li class="list-group-item">內容:' + t.msg + '</li>';
                    });
                }

                if(li_html.length > 0) {
                    document.body.style.overflow = 'hidden'; //關閉
                    $('#msg_dialog_ul').append(li_html);

                    setMsgDialog();
                    $('#msg_Dialog').dialog("open");
                }

            }, error(e) {
                console.log('getPushMSG=>error');
            }
        });
    }

    function setMsgRead() {
        var method = "post";
        var path = "/api/getpushmsg";
        $('#msg_Dialog').dialog('close');

        document.body.style.overflow = 'scroll'; // 開啟

        var id_str = '';
        $('#msg_dialog_ul .list-group-item-info').each(function (){

            var val = $(this).data('id');
            if(id_str !== '') id_str += ',';
            id_str += val;
        });
        var params = {
            p_userCode : $('#p_userCode').val(),
            fromtype : 'app_read',
            id_str : id_str
        };

        $.ajax({
            method: method,
            url: path,
            data: params,
            success: function (json) {
                console.log('success')


            }, error(e) {
                console.log('getPushMSG=>error');
            }
        });
    }

    function setMsgDialog() {
        var p_window_height = $(window).height();
        var p_window_width = $(window).width();
        var p_body_overflow_h = (p_window_height - 68) + 'px';
        console.log('p_body_overflow_h='+p_body_overflow_h);
        $('#msg_Dialog').removeClass('d-none');
        $('#msg_Dialog').dialog({
            autoOpen: false,
            width: p_window_width,
            height: p_window_height,
            open: function() {
                $('.ui-dialog-titlebar-close').hide(); // close ican
                $('.ui-dialog-content').css('height','auto'); //sign height
                $('.ui-dialog-content').css('padding','0'); //sign paddign
                $('.ui-dialog.ui-corner-all').css('height','100%'); //sign background height 100%

                // body 卷軸
                $('.ui-dialog').css('overflow-y','initial');
                $('#msg_Dialog .card-body').css('height', p_body_overflow_h);
                $('#msg_Dialog .card-body').css('overflow-y','auto');
            },
        });
    }


    function getSSID() {

        var ssid = '';
        try {
            ssid = app.getWifiSSID();

            if(ssid.search('unknown') > 0)
                ssid = '手機不支援'

            $('#ssid_val').val(ssid);
        } catch (e) {
            alert('請更新APP');
        }
    }


    // 新增 iframe pdf read 最後一頁 event
    function addIframeEvent(iframeId)
    {
        console.log('add id='+iframeId)
        var iframe = document.getElementById(iframeId);
        var parentWindow = document;
        parentWindow.chgReadType;
        iframe.contentDocument.getElementById('viewerContainer').addEventListener('scroll', (event) => {
            let viewH = iframe.contentDocument.getElementById('viewerContainer').offsetHeight
            let viewY = iframe.contentDocument.getElementById('viewerContainer').scrollTop
            let scrollH = iframe.contentDocument.getElementById('viewerContainer').scrollHeight
            if(scrollH - viewH - viewY < 50){
                parent.chgReadType(iframeId);
            }
        });
    }


    // iframe 條款閱讀，修改紀錄
    function chgReadType(iframeId){
        let idName = iframeId.replace('terms','label');
        idName = idName.replace('_iframe','');
        $('#'+idName).removeClass('alert-info');
        $('#'+idName).addClass('alert-success');
        $('#'+idName).html('讀取完成。');
        $('#'+idName).data('read','Y');
        let idNameAry = idName.split('_');
        let readTermApi = $('#label_'+idNameAry[1]+'_pdf').data('api');
        let readTerm = $('#label_'+idNameAry[1]+'_pdf').data('read');
        let readPclTerm = $('#label_'+idNameAry[1]+'_pcl_pdf').data('read');
        console.log('讀取確認>>'+idName)
        // console.log(readPclTerm)
        // console.log('#label_'+idNameAry[1]+'_pdf')
        // console.log('#label_'+idNameAry[1]+'_pcl_pdf')
        if(readTermApi == '' && readTerm == 'Y' && readPclTerm == 'Y') {
            $('#label_'+idNameAry[1]+'_pdf').data('api','Y');
            let vColumnName = idNameAry[1] == 'I'? 'termsi' : 'termsd';
            var params = {
                p_id : $('#p_id').val(),
                p_columnName : vColumnName,
            };
            apiEvent('termsPDFRead', params);
        }
    }


    // 設備借用、取回單API
    function equipmentAPI(id,columnName)
    {
        var params = new FormData(document.getElementById(id));
        var valAry = [];
        params.forEach(function(value, key){ valAry.push(key+'#'+value); });
        var valJson = JSON.stringify(valAry);
        var apiData = {
            p_companyNo : $('#p_companyNo').val(),
            p_workSheet : $('#p_workSheet').val(),
            p_custId : $('#p_custId').val(),
            p_value: valJson,
            p_columnName: columnName,
            p_id: $('#p_id').val(),
            p_userCode: $('#p_userCode').val(),
            p_userName: $('#p_userName').val(),
        };
        apiEvent(columnName, apiData);
    }


    // 判斷PDF閱讀，調整簽名欄位
    function chkPDFTreamRead()
    {
        let serviceName = $('#p_ServiceName').val();
        let serviceName2 = $('#p_serviceNameAry2').val();
        let vDRead = $('#label_D_pdf').data('rtime');
        let vIRead = $('#label_I_pdf').data('rtime');
        if(serviceName2.length > 0 || ['C HS','F CML'].indexOf(serviceName) >= 0) {
            // 有I類工單
            if(vIRead.length > 0 || vDRead.length > 0) {
                $('#signDiv_mcust').removeClass('d-none');
                if(vIRead.length > 0) {
                    $('#label_I_pdf').html('讀取完成');
                    $('#label_I_pcl_pdf').html('讀取完成');
                }
                if(vDRead.length > 0){
                    $('#label_D_pdf').html('讀取完成');
                    $('#label_D_pcl_pdf').html('讀取完成');
                }
                valueToSelectFmMcustSign();
            }
            else {
                $('#signDiv_mcust').addClass('d-none');
            }

        } else {
            if(vDRead.length > 0) {
                $('#signDiv_mcust').removeClass('d-none');
                $('#label_D_pdf').html('讀取完成');
                $('#label_D_pcl_pdf').html('讀取完成');
                valueToSelectFmMcustSign();
            }
            else {
                $('#signDiv_mcust').addClass('d-none');
            }
        }
    }


    // 更新select，用戶簽名，對象選則
    function valueToSelectFmMcustSign()
    {
        let str = '{{ data_get($p_data['twmbbcheck'],'sign_mcust_select') }}';
     // console.log(str)
     // console.log(str.length)
        if(str.length > 0)
        $('#selectMcustUser option[value="'+str+'"]').prop('selected',true);
    }


    // 借用單[DB to HTML]
    function borrowminglistSetOption()
    {
        var json = $('#p_BorrowmingList').val();
        if(json == '') return true;
        var obj = JSON.parse(json);
        var res = [];
        for(var i in obj) {
            res.push(obj[i]);
        }
        if(res.length > 0) {
            Object.entries(res[0]).forEach(entry => {
                const [k, t] = entry;
                $('#borrowminglist_form select[name="'+t.id+'"] option[value="'+t.qty+'"]').prop('selected',true)
            });
        }
    }


    // 取回單[DB to HTML]
    function retrieveListSetOption()
    {
        var json = $('#p_RetrieveList').val();
        if(json == '') return true;
        var obj = JSON.parse(json);
        var res = [];
        for(var i in obj) {
            res.push(obj[i]);
        }
        if(res.length > 0) {
            Object.entries(res[0]).forEach(entry => {
                const [k, t] = entry;
                $('#retrievelist_form select[name="'+t.id+'"] option[value="'+t.qty+'"]').prop('selected',true)
            });
        }
    }


    // 用戶簽名對象，選擇確認
    function signUserSelAPI()
    {
        if($('#selectMcustUser').val() == '本人簽名')
            $('#sign_mcust_desc01').addClass('d-none');
        else
            $('#sign_mcust_desc01').removeClass('d-none');

        var params = {
            p_columnName : "sign_mcust_select",
            p_value : $('#selectMcustUser').val(),
            p_id : $('#p_id').val()
        }
        apiEvent('sign_cust_select',params);
    }


    // 更換設備
    function changeEquipment()
    {
        var val_siginsn = $("input[name='chg_siginsn']:checked").data('sn');
        //console.log(val_siginsn);
        if(typeof(val_siginsn) === "undefined") {
            alert( "請選取切換的設備!");
            return false;
        }
        stbApi('chgEquipment')
    }


    // 新增，維修工單(213 幹線組)
    function addWorkerMain(subsid)
    {
        let url = '/api/EWO/addWorkerMaintain';
        let data = [];
        data['id'] = $('#p_id').val();
        data['companyNo'] = $('#p_companyNo').val();
        data['workSheet'] = $('#p_workSheet').val();
        data['subsId'] = subsid;
        data['custId'] = $('#p_custId').val();
        data['workCause'] = $('#select_srviceReasonLast').val();
        data['remark'] = $('#select_srviceReasonLast').val();
        data['workKind'] = $('#p_workkind').val();
        data['userCode'] = $('#p_userCode').val();
        data['userName'] = $('#p_userName').val();
        data['contactName'] = $('#p_custName').val();
        data['contactCell'] = $('#p_userMobile').val();
        data['bookdate'] = '{{ date('Y-m-d') }}';
        data['timing'] = '{{ date('H:i:s') }}';
        data['workTeam'] = '213 幹線組';
        let dataJson = JSON.stringify(Object.assign({}, data));

        if(data['workCause'] == '' || data['workCause'] == '請選擇'){
            alert('請選擇[維修原因]')
            return;
        }

        $.ajax({
            url: url,
            type: 'post',
            headers: {"Content-Type": "application/json"},
            data: dataJson,
            success: function (result) {
                $('#addWorkerMain_label').text('結果:'+result.data);
                if(result.code != '0000') {
                    alert('新增失敗，'+result.data);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('新增[幹線]工單，失敗');
            }
        });
    }


    // 新增，查詢參數log
    function addStbMAtvTestValueLog(scanValId,type) {
        console.log('scanValId='+scanValId);
        var url = '/api/EWO/addCheckValeuLog';
        let date = new Date();
        let time = date.getTime();
        var scanVal = $('#'+scanValId);
        var listObj = $('#stbmatvdevicvalue_list');
        let datalist = [];
        datalist['nodeNo'] = $('#p_nodeNo').val();
        datalist['invUnifyNo'] = $('#p_invUnifyNo').val();
        datalist['linkId'] = $('#p_linkId').val();
        let datalistStr = JSON.stringify(Object.assign({}, datalist));

        let data = [];
        data['userCode'] = $('#p_userCode').val();
        data['userName'] = $('#p_userName').val();
        data['companyNo'] = $('#p_companyNo').val();
        data['custId'] = $('#p_custId').val();
        data['workSheet'] = $('#p_workSheet').val();
        data['type'] = type;
        data['info'] = scanVal.val();
        data['caseId'] = 'stbmatvdevicvalue_id_'+time;
        data['source'] = 'ewoapp';
        data['datalist'] = datalistStr;
        switch (type) {
            case 'stb_atvqrcode':
                // 開通的qr Code
                scanVal.val('請掃描 STB or ATV 電視QrCode');

                data['serviceName'] = '3 DSTB';
                if(scanValId != 'openstbmatvdevicvalue_scanstr') {
                    data['subsId'] = scanVal.data('subsid');
                }

                let html = '';
                html += '<li class="list-group-item pt-0 pb-0 list-group-item-info">'+date+'</li>';
                html += '<li class="list-group-item pt-0 pb-0">掃描內容：</li>';
                html += '<li class="list-group-item pt-0 pb-0">'+data['info']+'</li>';
                if(data['info'].search('Facisno:') >= 0) {
                    html += '<li class="list-group-item pt-0 pb-0" id="'+data['caseId']+'">新增中</li>';
                    listObj.prepend(html);
                } else {
                    html += '<li class="list-group-item pt-0 pb-0">結果：格式不合格[不含Facisno]</li>';
                    listObj.prepend(html);
                    return;
                }
                break;
            case 'querycminfo':
                let id02 = scanValId.replace('_value','_btn');
                data['subsId'] = $('#'+id02).data('subsid');
                data['serviceName'] = $('#p_serviceNameAry2').val();
                break;
        }


        var dataJson = JSON.stringify(Object.assign({}, data));


        $.ajax({
            url: url,
            type: 'post',
            headers: {"Content-Type": "application/json"},
            data: dataJson,
            success: function (json) {
                console.log(json)
                let data = json.data;
                let caseId = data.caseId;
                let code = json.code;
                let logMsg = code == '0000'? '紀錄新增完成' : '紀錄新增失敗';
                let chkCode = data.code;
                let chkCodeMsg = chkCode == '0000'? '參數檢查OK' : '參數檢查不好';
                let chkMsg = data.msg;
                let msg = logMsg+';<br>備註：'+chkCode+''+chkMsg+'。';
                if(type == 'stb_atvqrcode') {
                    let v_datalist = JSON.parse(data.datalist);
                    console.log(v_datalist);
                    $.each(v_datalist,function(k,t){
                        msg += '<br>'+k+' = '+t;
                    });
                }
                $('#'+caseId).html(msg);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('紀錄失敗');
            }
        });
    }


    // 入戶、離開時間
    function saveDorTime(type) {
        let url = '/api/EWO/upOrderListColumn';
        const d = new Date()
        d.setHours(d.getHours() + 8)
        let p_time = d.toJSON().substr(0,19).replace('T',' ');
        var strAry = [];
        strAry['In'] = '入戶';
        strAry['Out'] = '離戶';

        if(type == 'Out')
        if(!confirm('確認離開用戶家!!?\n\n***提醒***\n確認離戶後,\n訂單管理中心將持續與用戶聯繫,\n並另派人員處理此工單'))
            return false;

        let data = [];
        data['id'] = $('#p_id').val();
        data['companyNo'] = $('#p_companyNo').val();
        data['workSheet'] = $('#p_workSheet').val();
        data['custId'] = $('#p_custId').val();
        data['userCode'] = $('#p_userCode').val();
        data['userName'] = $('#p_userName').val();
        data['eventType'] = 'dorTime';
        data['request'] = strAry[type]+'時間'+p_time;
        data['responses'] = 'OK';
        data['columnName'] = 'dorTime'+type;
        data['value'] = p_time;
        var dataJson = JSON.stringify(Object.assign({}, data));
        $.ajax({
            url: url,
            type: 'post',
            headers: {"Content-Type": "application/json"},
            data: dataJson,
            success: function (json) {
                // let strAry = [];
                let str = strAry[type]+'確認，紀錄時間:'+json.data+'；'+json.date;
                if(json.code != '0000')
                    str = '異常 '+json.code+'；'+'#'+json.data;
                $('#labelDor'+type).text(str);

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('紀錄失敗');
            }
        });
    }

    function addDstbRemoteMapping(scanValId)  {
        console.log('scanValId='+scanValId);
        // 對應的 Scan ID
        var url = '/api/EWO/addDstbRemoteMapping';
        let item = $('#'+scanValId);

        let data = [];
        data['userCode'] = $('#p_userCode').val();
        data['userName'] = $('#p_userName').val();
        data['CompanyNo'] = $('#p_companyNo').val();
        data['CustID'] = $('#p_custId').val();
        data['AssignSheet'] = $('#p_workSheet').val(); // ewo 的 worksheet 為 ms0301 的 assignsheet ，比較新的都直接使用 assignsheet
        data['remoteQrCode'] = item.val();
        data['SubsID'] = item.data('subs_id');
        data['SingleSN'] = item.data('single_sn');


        var dataJson = JSON.stringify(Object.assign({}, data));


        $.ajax({
            url: url,
            type: 'post',
            headers: {"Content-Type": "application/json"},
            data: dataJson,
            success: function (json) {
                //
                if (json.code !== '0000') {
                    alert('更新遙控器 QR code 失敗，請稍後再試');
                    return false;
                }
                item.attr('placeholder',json.data.remoteQrCode);
                item.val(json.data.remoteQrCode);
                let itemParentLi = item.parent('.DSTBScanLi');

                // 替換底色 class
                if (itemParentLi.hasClass('list-group-item-danger')) {
                    itemParentLi.removeClass('list-group-item-danger');
                    itemParentLi.addClass('list-group-item-success');
                }
                let itemParentLiHtml = itemParentLi.html();
                itemParentLiHtml = itemParentLiHtml.replace('尚未綁定','已綁定(可以再次掃描更新)');
                itemParentLi.html(itemParentLiHtml);

                // 顯示字段替換為已綁定
                let data = json.data;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('紀錄失敗');
            }
        });
    }



</script>
