<?php

return [

    // 遲到說明
    'delateDesc' => [
        'id01' => '路上塞車01'
        ,'id02' => '路上塞車02'
        ,'id03' => '路上塞車03'
        ,'id04' => '路上塞車04'
    ]

    // 退單，API代號
    ,'CahrgeBackType' => [
        '5 維修' => '8'

        ,'1 裝機' => '4'
        ,'2 復機' => '4'
        ,'6 移機' => '4'
        ,'8 工程收費' => '4'
        ,'9 停後復機' => '4'
        ,'A 加裝' => '4'
        ,'C 換機' => '4'

        ,'3 拆機' => '11'
        ,'4 停機' => '11'
        ,'7 移拆' => '11'
        ,'H 退拆設備' => '11'
        ,'I 退拆分機' => '11'
        ,'K 退次週期項' => '11'
        ,'U 到宅取設備' => '11'
    ]

    , 'SO_CrmID' => [
          '210' => '5'
        , '220' => '7'
        , '230' => '8'
        , '240' => '9'
        , '250' => '3'
        , '270' => '10'
        , '310' => '6'
        , '620' => '1'
        , '610' => '2'
        , '720' => '11'
        , '730' => '13'
        , '209' => '15'
    ]

    // serviceName
    ,'ServiceNameList' => ['1 CATV','2 CM','3 DSTB','C HS','D TWMBB','B FTTH']

    // TITLE
    ,'title' => env('EWO_TITLE', 'EWO-D')

    // STB API
    ,'STB_API' => env('STB_API', '')

    // document dir
    ,'DOCUMENT_ROOT' => dirname($_SERVER['DOCUMENT_ROOT'])

    // PDF 版本
    ,'PDF_CODE_V' => 'v3'

    // PDF(FET) 版本
    ,'PDF_CODE_FET_V' => 'v3fet'

    // PDF 條款 版本
    ,'PDF_TERMS_V' => '20230208'

    // OEMS r1 URL
    ,'R1_URL' => env('R1_URL','')

    // EWO URL
    ,'EWO_URL' => env('EWO_URL','https://ewo-app.hmps.cc')

    // EWO[226] URL
    ,'EWO_226_URL' => env('EWO_226_URL','https://ewo-s.hmps.cc')

    ,'workKind' =>[
        '1 裝機'=>'裝機',
        '2 復機'=>'裝機',
        '6 移機'=>'裝機',
        '8 工程收費'=>'裝機',
        '9 停後復機'=>'裝機',
        'A 加裝'=>'裝機',
        'C 換機'=>'裝機',

        '3 拆機'=>'拆機',
        '4 停機'=>'拆機',
        '7 移拆'=>'拆機',
        'H 退拆設備'=>'拆機',
        'I 退拆分機'=>'拆機',
        'K 退次週期項'=>'拆機',
        'U 到宅取設備'=>'拆機',

        '5 維修'=>'維修',
    ]

    ,'workKindIns' =>[
        '1 裝機'
        ,'2 復機'
        ,'6 移機'
        ,'8 工程收費'
        ,'9 停後復機'
        ,'A 加裝'
        ,'C 換機'
    ]

    ,'workKindDel' =>[
        '3 拆機'
        ,'4 停機'
        ,'7 移拆'
        ,'H 退拆設備'
        ,'I 退拆分機'
        ,'K 退次週期項'
        ,'U 到宅取設備'
    ]

    ,'workKindMai' =>['5 維修']

    ,'SQL_DEBUG' => env('SQL_DEBUG',false)

    ,'SEtDeviceChargeName' => array(
        '03660 USB無線網卡(EDIMAX)',
        '03670 bb-無線AP(借)',
        '25168 2台組-M4-MESH(CM綁定)',
        '25158 M4-MESH(CM綁定1)',
        '25159 M4-MESH(CM綁定1.)',
        '25169 1台組-M4-MESH(租借)',
        '25251 M4-MESH(租借)1',
        '25252 M4-MESH(租借)2',
        '25253 M4-MESH(租借)3',
        '25254 M4-MESH(租借)4',
        '25215 1台組-M4-MESH-(租金)',
        '26833 GOOGLE智慧音箱(NEST-AUDIO)',
        '26831 宏碁智慧音箱(HSP-3100G)',
    )

    ,'MESHAPModelList' => array(
        'Deco M9 Plus Mesh',
        'Deco M5 Mesh',
        'Deco M4 Mesh',
        'Linksys Velop 雙頻 AC1300 Mesh',
        'COVR-1100 AC-1200雙頻MESH WIFI',
        'M15/AX1500雙頻MESH WIFI',
        'WiFi-6 AP D-Link DIR-X1860',
        'TP-Link Deco X20',
        'TP-Link Archer A6 AC1200',
    )

    ,'CompanyNoAry' => array('209','210','220','230','240','250','270','310','610','620','720','730')

    ,'CompanyNoStrAry' => array(
        "220,230,240" => "台北",
        "209,250,270" => "新北",
        "610,620" => "台南",
        "720,730" => "高雄",
        "999" => "//////////////////////",
        "209" => "數位天空",
        "210" => "吉隆",
        "220" => "長德",
        "230" => "萬象",
        "240" => "麗冠",
        "250" => "新視波",
        "270" => "家和",
        "310" => "北健",
        "610" => "雙子星",
        "620" => "三冠王",
        "720" => "慶聯",
        "730" => "港都",
    )
    , 'checkOutImg' => array(
        'cg' => '服裝和美容',
        'vc' => '車輛清潔',
        'tc' => '工具清洗',
        'le' => '勞安設備',
    )

    , 'COSSDBTYPE' => env('COSSDBTYPE','COSSDB')

    // 頻寬
    , 'BandwidthH' => 500
    , 'BandwidthL' => 100

];
