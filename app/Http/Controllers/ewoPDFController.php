<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use PDF;
use domPDF;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ewoPDFController extends Controller
{


  public function createPDF2(Request $request)
  {
      //$pdf = App::make('snappy.pdf.wrapper');
      if(10) {


          $pdf = PDF::loadHTML('<h1>Test</h1>');
          return $pdf->download(public_path('test0817/'.'test0817-1609.pdf'));
      }

      if(0) {
          $pdf = APP::make('snappy.pdf');
          $pdf->loadHTML('<h1>Test</h1>');
          return $pdf->inline();
      }
      if(0) {
          return PDF::loadFile('http://www.google.com')->inline(public_path('test0817/'.'test0817-1609.pdf'));
      }


  }



    public function createPDF($type,Request $request)
    {
        //$pdf = App::make('snappy.pdf.wrapper');
        $day = date('md');
        $time = date('His');
        $data = array('a','b','c');

        //echo "$type;$time;$day,$time,".public_path("test$day/"."test$day-$time".'.pdf').'<hr>';

        // download
        if($type === 'TesseractOCR') {
        //logo_05.jpg
            $file = public_path('img/logo_05.jpg');
            //$file = 'img/logo_05.jpg';
//            $obj = new TesseractOCR($file);
//            $str = $obj->run();

//            echo '#'.$str.'#';

            $file = 'img/id.jpg';
            $file = public_path('/upload/25382552_20211123/identity_01.jpg');
//            $orc =  (new TesseractOCR($file))->lang('chi_tra')->run();
            $orc = '';
            echo $orc;
        }
        if($type === '1') {
            $pdf = PDF::loadHTML('<h1>Test</h1>');
            return $pdf->inline();
        }
        if($type === '11') {
            $pdf = PDF::loadHTML('<h1>Test</h1>');
            $pdf->loadView('pdf.pdf_test102', $data);
            return $pdf->inline();
        }
        if($type === '13') {
            $html = "
            @include('pdf.pdf_test101')
            @include('pdf.pdf_test102')

            ";
            $pdf = PDF::loadHTML(html_entity_decode($html));
            return $pdf->inline();
        }
        if($type === '132') {
            $html = "
            @include('pdf.pdf_test101')
            @include('pdf.pdf_test102')

            ";
            $pdf = PDF::loadHTML(htmlspecialchars_decode($html));
            return $pdf->inline();
        }
        if($type === '12') {

            $pdf = PDF::loadView('pdf.pdf_test101', compact('data'));
            return $pdf->download("test$day.pdf");
        }

        // save loadview01
        if($type === '4') {
            $pdf = PDF::loadView('pdf.pdf_test101', $data);
            $pdf->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0);
            $pdf->save(public_path("test$day/"."test$day-$time".'.pdf'));
        }

        // save loadview01+02
        if($type === '5') {
            $pdf = PDF::loadView('pdf.pdf_test101', $data);
            $pdf->loadView('pdf.pdf_test102', $data);
            $pdf->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0);
            $pdf->save(public_path("test$day/"."test$day-$time".'.pdf'));
        }
        if($type === '52') {
            $pdf = PDF::loadView('pdf.pdf_test111', $data);
            $pdf->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0);
            $pdf->save(public_path("test$day/"."test$day-$time".'.pdf'));
        }

        if($type === '3') {
            PDF::loadView('pdf.pdf_test101', $data)->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0)->save(public_path("test$day/"."test$day-$time".'.pdf'));
        }

        if($type == '08201134')
        {
            $in_file = public_path('test0819/a.pdf');
            $out_file = public_path('test0819/a02.pdf');
            $data = [
            ];
            $pdf = mPDF::loadView('pdf.pdf_test111', $data);
            $pdf->SetProtection(['copy', 'print'], '', 'pass');
            $pdf->stream($out_file);
        }

        if($type == '08201316')
        {
            //return view('pdf.pdf_test101');
            $data = ['a','b','c'];
            $pdf = domPDF::loadView('pdf.pdf_test101',compact('data'));


            $pdf->save(public_path('test0820.pdf'))->stream('download.pdf');
        }

        if($type == '082013162')
        {
            $data = ['a','b','c'];

            $pdf = domPDF::loadView('pdf.pdf_test101',compact('data'));

            $pdf->setOptions(['adminPassword' => ''])->setEncryption('4567');

            $pdf->save(public_path('test0820.pdf'));
        }

        if($type == '082013164')
        {
            $data = ['a','b','c'];

            $pdf = domPDF::loadView('pdf.pdf_test101',compact('data'));

            $pdf->setOptions(['adminPassword' => ''])->setEncryption('4567');

            $pdf->getDomPdf()->getOptions()->set('enable_php', true);

            $pdf->save(public_path('test0820.pdf'));
        }

        if($type == '082013165')
        {

            $data = ['a甲','b乙','c丙',rand()];

            $pdf = domPDF::loadView('pdf.pdf_test101',compact('data'));

            $pdf->setPaper('A4', 'landscape');

            $pdf->setOptions(['adminPassword' => '','isRemoteEnabled'=>true])->setEncryption('4567');


            $pdf->save(public_path('test0820.pdf'));
        }

        if($type == '082013166')
        {

            $data = ['a甲','b乙','c丙',rand()];

            $html = '<div class="border-1">
    <div class="w-30 bg-info">adiv</div>
    <div class="w-40 bg-warning">bdiv</div>
    <div class="w-30 bg-info">cdiv</div>
</div>';
            $pdf = domPDF::loadHTML($html);

            //$pdf->setPaper('A4', 'landscape');

            $pdf->stream('my.pdf',array('Attachment'=>0));
        }

        if($type == '0906')
        {

            $html = '
<div class="border-1">
    <div class="w-30 bg-info"><img src="https://ewo-app.hmps.cc/img/logo_01.png"></div>
    <div class="w-40 bg-warning">bdiv</div>
    <div class="w-30 bg-info">cdiv</div>
</div>
';
            $pdf = domPDF::loadHTML($html);

            $pdf->setPaper('A4', 'landscape');

            $pdf->save(public_path('t-0906'.rand().'.pdf'));


//            $pdf->stream(public_path('t-0906pdf'),array('Attachment'=>0));
        }


        if($type == '09071103')
        {

            $data = ['a甲','b乙','c丙',rand()];

            $pdf = domPDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $pdf->loadView('pdf.pdf_test101',compact('data'));

            $pdf->setPaper('A4', 'landscape');

            $pdf->setOptions(['adminPassword' => '','isRemoteEnabled'=>true])->setEncryption('4567');


            $pdf->save(public_path('test0820.pdf'));
        }

        if($type == '09071109')
        {

            $data = ['a甲','b乙','c丙',rand()];

            $pdf = domPDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $pdf->loadView('pdf.pdf_test101',compact('data'));

            $pdf->setPaper('A4', 'landscape');

            //$pdf->setOptions(['adminPassword' => '','isRemoteEnabled'=>true])->setEncryption('4567');


            $pdf->save(public_path('test0820.pdf'));
        }


    }


}
