<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreFileRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateFileRequest;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files=File::all();
        $count=$files->count();
        $downloads=[];
        if (count($files)){
            foreach ($files as $key => $file) {
                $downloads[]=Storage::url("$file->path");
            }
        } 
        else {
            $downloads = array_fill(0, $count, '');
        }
        // dd($files,$count,$downloads);
        return view('adminfiles')->with("message","Welcome Dear admin  ".auth()->user()->email)->with("files",$files)->with("count",$count)->with("downloads",$downloads);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFileRequest $request)
    {
        $request->validated();
        $path = Storage::disk("local")->put('public', $request->file('uploadedfile'));

        // Scan file

        // curl --location --request POST 'https://api.virusscannerapi.com/virusscan' \
        // --header 'X-ApplicationID: {{X-ApplicationID}}' \
        // --header 'X-SecretKey: {{X-ApplicationID}}' \
        // --form 'inputFile="https://virusbotaccount.blob.core.windows.net/automatedtestfiles/demo.docx"' \
        // --form 'async="false"'

        // $response1 = Http::post('https://api.virusscannerapi.com/virusscan', [
        //     'async' => 'false',
        // ]);
        // $response2 = Http::asForm()->post('https://api.virusscannerapi.com/virusscan', [
        //     'async' => 'false',
        // ]);
        // $response3 = Http::attach(
        //     'attachment', $request->file('uploadedfile'), 'uploadedfile'
        // )->withHeaders([
        //     'X-ApplicationID' => '{{X-629c66f9-a0aa-49ee-ba41-e605b64b0685}}',
        //     'X-SecretKey' => '{{X-9b62cab4-c214-4ba9-89b8-c59e5c4298f0}}'
        // ])->post('https://api.virusscannerapi.com/virusscan');
        $response3 = Http::timeout(30)->withHeaders([
            'X-ApplicationID' => '{{X-629c66f9-a0aa-49ee-ba41-e605b64b0685}}',
            'X-SecretKey' => '{{X-9b62cab4-c214-4ba9-89b8-c59e5c4298f0}}'
        ])->post('https://api.virusscannerapi.com/virusscan/scanningProcessId',[
            "inputFile" => "https://virusbotaccount.blob.core.windows.net/automatedtestfiles/demo.docx",
            "async" =>"false"
        ]);


        dd($response3);

        
        // Check if the new file existed in buyer files or not?
        $userfiles=User::find(Auth()->user()->id)->files()->get();
        $newfile=$request->file('uploadedfile');
        $checkRepeatFile=false;
        $resultOfCheck=[];
        foreach ($userfiles as $key => $file) {
            if (md5_file($newfile)==$file->hashedfile){
                $checkRepeatFile=true; 
                $resultOfCheck["duplicate"]="Your file is as same as previous file";
            }
        }
        if(!$checkRepeatFile){
            $createfilerecord=User::find(Auth()->user()->id)->files()->create([
                "name"=>$request->input("name")??"undefined",
                "path"=>$path,
                "size"=> $_FILES['uploadedfile']['size'],
                "hashedfile"=>md5_file($newfile),
            ]);
            $resultOfCheck["duplicate"]="Your file is unique.";
        }

        // route to main page
        $files=User::find(auth()->user()->id)->files(Auth()->user())->get();
            $count=$files->count();
        return view('buyerfirstpage')->with("message","Welcome Dear Buyer  ".auth()->user()->email)->with("files",$files)->with("count",$count)->with("resultOfCheck",$resultOfCheck);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFileRequest  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }
}
