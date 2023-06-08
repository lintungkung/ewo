
<style>
    @page {
        size: a4;
        /*margin: 0;*/
    }
    @font-face {
        font-family: SimHei;
        src: url('{{ asset("font/SimHei.ttf") }}') format('truetype');
    }

    * {
        font-family: SimHei;
    }

    #table101 {
        width:100%;
        border-collapse: collapse;
    }

    /* new page */
    .page_break { page-break-before: always; }

    #table101 tr td {
        border: 1px solid black;
    }

    .border-white {
        border: 1px solid white !important;
    }

    .border-black {
        border: 1px solid black !important;
    }
    .border-collapse {
        border-collapse: collapse;
    }


    .h-10p {height: 10px;}
    .h-15p {height: 15px;}
    .h-20p {height: 20px;}
    .h-30p {height: 30px;}
    .h-50p {height: 50px;}
    .h-70p {height: 70px;}
    .h-90p {height: 90px;}
    .h-100p {height: 100px;}
    .h-200p {height: 200px;}
    .h-300p {height: 300px;}

    .h-25 {height: 25% !important;}
    .h-50 {height: 50% !important;}
    .h-75 {height: 75% !important;}
    .h-100 {height: 100% !important;}

    .w-25 {width: 25% !important;}
    .w-30 {width: 30%; !important;}
    .w-40 {width: 40%; !important;}
    .w-50 {width: 50% !important;}
    .w-75 {width: 75% !important;}
    .w-80 {width: 80% !important;}
    .w-90 {width: 90% !important;}
    .w-100 {width: 100% !important;}


    .wh-10p {width: 10px;}
    .w-15p {width: 15px;}
    .w-20p {width: 20px;}
    .w-30p {width: 30px;}
    .w-50p {width: 50px;}
    .w-70p {width: 70px;}
    .w-90p {width: 90px;}
    .w-100p {width: 100px;}
    .w-150p {width: 150px;}
    .w-200p {width: 200px;}
    .w-300p {width: 300px;}

    .float-left {float: left;}
    .float-right {float: right;}
    .text-center {text-align: center;}
    .text-right {text-align: right;}
    .text-left {text-align: left;}
    .text-top {vertical-align: text-top;}
    .text-bot {vertical-align: bottom;}

    .text-im0 {text-indent : -0px; margin-left: 0px;}
    .text-im15 {text-indent : -15px; margin-left: 15px;}
    .text-im20 {text-indent : -20px; margin-left: 20px;}
    .text-im25 {text-indent : -25px; margin-left: 25px;}
    .text-im30 {text-indent : -30px; margin-left: 30px;}
    .text-im35 {text-indent : -35px; margin-left: 35px;}
    .text-im40 {text-indent : -40px; margin-left: 40px;}
    .text-im45 {text-indent : -45px; margin-left: 45px;}
    .text-im50 {text-indent : -50px; margin-left: 50px;}

    .text-i-15 {text-indent : 15px;}
    .text-i-20 {text-indent : 20px;}
    .text-i-25 {text-indent : 25px;}
    .text-i-30 {text-indent : 30px;}

    .red {color: red;}

    .align-text-bottom {vertical-align: text-bottom !important;}
    .align-text-top {vertical-align: text-top !important;}

    .align-top {vertical-align: top!important;}
    .align-bottom {vertical-align: bottom!important;}

    .align-items-center {
        -ms-flex-align: center!important;
        align-items: center!important;
    }

    .font-s5 {font-size: 5px;}
    .font-s8 {font-size: 8px;}
    .font-s7 {font-size: 7px;}
    .font-s9 {font-size: 9px;}
    .font-s10 {font-size: 10px;}
    .font-s11 {font-size: 11px;}
    .font-s12 {font-size: 12px;}
    .font-s13 {font-size: 13px;}
    .font-s15 {font-size: 15px;}
    .font-s18 {font-size: 18px;}
    .font-s20 {font-size: 20px;}
    .font-s25 {font-size: 25px;}
    .font-s30 {font-size: 30px;}

    .line-h08 {line-height: 0.8;}
    .line-h10 {line-height: 1;}

    .row {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    .col-12 {
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 100%;
    }
    .col-6 {
        -ms-flex: 0 0 50%;
        flex: 0 0 50%;
        max-width: 50%;
    }

    /*.col-1 {width: 16.666666%;}*/
    /*.col-2 {width: 16.666666%;}*/
    /*.col-3 {width: 25%;}*/
    /*.col-4 {width: 33.333333%;}*/
    /*.col-5 {width: 33.333333%;}*/
    /*.col-6 {width: 33.333333%;}*/

    .col-1 {
        flex: 0 0 auto;
        width: 8.33333333%;
    }
    .col-2 {
        flex: 0 0 auto;
        width: 16.66666667%;
    }
    .col-3 {
        flex: 0 0 auto;
        width: 25%;
    }
    .col-4 {
        flex: 0 0 auto;
        width: 33.33333333%;
    }
    .col-5 {
        flex: 0 0 auto;
        width: 41.66666667%;
    }
    .col-6 {
        flex: 0 0 auto;
        width: 50%;
    }
    .col-7 {
        flex: 0 0 auto;
        width: 58.33333333%;
    }
    .col-8 {
        flex: 0 0 auto;
        width: 66.66666667%;
    }
    .col-9 {
        flex: 0 0 auto;
        width: 75%;
    }
    .col-10 {
        flex: 0 0 auto;
        width: 83.33333333%;
    }
    .col-11 {
        flex: 0 0 auto;
        width: 91.66666667%;
    }
    .col-12 {
        flex: 0 0 auto;
        width: 100%;
    }

    .wordBreakAll {word-break: break-all;!important;}
    .wordWrap {word-wrap:break-word;!important;}

    .m-0 {margin: 0;}
    .m-1 {margin: .25rem!important;}
    .m-2 {margin: .5rem!important;}
    .m-3 {margin: 1rem!important;}
    .m-4 {margin: 1.5rem!important;}
    .m-5 {margin: 3rem!important;}

    .mt-0,.px-0{margin-top:0!important}
    .mt-1,.px-1{margin-top:.25rem!important}
    .mt-2,.px-2{margin-top:.5rem!important}
    .mt-3,.px-3{margin-top:1rem!important}
    .mt-4,.px-4{margin-top:1.5rem!important}
    .mt-5,.px-5{margin-top:3rem!important}

    .mb-0,.px-0{margin-bottom:0!important}
    .mb-1,.px-1{margin-bottom:.25rem!important}
    .mb-2,.px-2{margin-bottom:.5rem!important}
    .mb-3,.px-3{margin-bottom:1rem!important}
    .mb-4,.px-4{margin-bottom:1.5rem!important}
    .mb-5,.px-5{margin-bottom:3rem!important}

    .mr-1,.px-1{margin-right:.25rem!important}
    .mr-2,.px-2{margin-right:.5rem!important}
    .mr-3,.px-3{margin-right:1rem!important}
    .mr-4,.px-4{margin-right:1.5rem!important}
    .mr-5,.px-5{margin-right:3rem!important}

    .ml-1,.px-1{margin-left:.25rem!important}
    .ml-2,.px-2{margin-left:.5rem!important}
    .ml-3,.px-3{margin-left:1rem!important}
    .ml-4,.px-4{margin-left:1.5rem!important}
    .ml-5,.px-5{margin-left:3rem!important}
    .ml-15p{margin-left: 15px;}
    .ml-20p{margin-left: 20px;}
    .ml-25p{margin-left: 25px;}
    .ml-30p{margin-left: 30px;}
    .ml-35p{margin-left: 35px;}
    .ml-40p{margin-left: 40px;}
    .ml-45p{margin-left: 45px;}
    .ml-50p{margin-left: 50px;}
    .ml-55p{margin-left: 55px;}
    .ml-60p{margin-left: 60px;}
    .ml-70p{margin-left: 70px;}
    .ml-80p{margin-left: 80px;}

    .p-0 {padding: 0;}
    .p-1 {padding: .25rem!important;}
    .p-2 {padding: .5rem!important;}
    .p-3 {padding: 1rem!important;}
    .p-4 {padding: 1.5rem!important;}
    .p-5 {padding: 3rem!important;}

    .pt-0,.px-0{padding-top:0!important}
    .pt-1,.px-1{padding-top:.25rem!important}
    .pt-2,.px-2{padding-top:.5rem!important}
    .pt-3,.px-3{padding-top:1rem!important}
    .pt-4,.px-4{padding-top:1.5rem!important}
    .pt-5,.px-5{padding-top:3rem!important}

    .pb-0,.px-0{padding-bottom:0!important}
    .pb-1,.px-1{padding-bottom:.25rem!important}
    .pb-2,.px-2{padding-bottom:.5rem!important}
    .pb-3,.px-3{padding-bottom:1rem!important}
    .pb-4,.px-4{padding-bottom:1.5rem!important}
    .pb-5,.px-5{padding-bottom:3rem!important}

    .pr-1,.px-1{padding-right:.25rem!important}
    .pr-2,.px-2{padding-right:.5rem!important}
    .pr-3,.px-3{padding-right:1rem!important}
    .pr-4,.px-4{padding-right:1.5rem!important}
    .pr-5,.px-5{padding-right:3rem!important}

    .pl-1,.px-1{padding-left:.25rem!important}
    .pl-2,.px-2{padding-left:.5rem!important}
    .pl-3,.px-3{padding-left:1rem!important}
    .pl-4,.px-4{padding-left:1.5rem!important}
    .pl-5,.px-5{padding-left:3rem!important}

</style>
