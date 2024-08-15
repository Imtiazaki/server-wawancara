<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ScoringRequest;
use App\Http\Requests\SubmitRequest;
use App\Http\Requests\TemplateRequest;
use App\Http\Resources\ScoringResource;
use App\Models\Scoring;
use App\Models\Template;

class ScoringController extends Controller
{
    public function getData(ScoringRequest $request)
    {
        $data = $request->validated();
        if ($data['assessor'] != 'ALL') {
            $scores = Scoring::where('assessor', $data['assessor'])->get();
        } else {
            $scores = Scoring::all();
        }

        $collection = collect($scores);
 
        $multiplied = $collection->map(function ($item) {
            $findtemplate = Template::where('nama', $item['template'])->first();
            $item->form = $findtemplate;
            return $item;
        });
 
        $multiplied->all();
        

        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'data' => $scores
        ], 200);
    }

    public function getDataByIds($ids)
    {
        $id = explode('-', $ids);
        $array = array();
        foreach ($id as $data) {
            $target = Scoring::find($data);
            if ($target) {
                array_push($array, $target);
            };
        }

        
        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'data' => $array
        ], 200);
    }

    public function getFormByName(TemplateRequest $request)
    {
        $data = $request->validated();;
        $form = Template::where('nama', $data['nama'])->first();

        

        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'data' => $form,
        ], 200);
    }

    public function registerScore(ScoringRequest $request) {

        $data = $request->validated();

        $form = Template::where('nama', $data['template'])->first();
        $steps = explode(':X:', $form['steps']);
        $choices = explode(':X:', $form['choices']);
        $array = array();
        $euphoria = array();

        foreach ($steps as $step) {
            array_push($array, '0:0:0:0:0');
            array_push($euphoria, '0');
        };

        $user = Scoring::create([
            'nama' => $data['nama'],
            'scores' => join(":X:",$euphoria),
            'sistem' => join(":X:",$array),
            'revisi' => null,
            'template' => $data['template'],
            'assessor' => $data['assessor'],
            'tanggal' => null,
            'status' => 'NOT STARTED',
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function submitScoring(SubmitRequest $request, $ids)
    {
        $data = $request->validated();
        $sistem = explode(':Y:', $data['scores']);
        $rating = explode(':Y:', $data['rating']);
        $date = explode(':Y:', $data['tanggal']);
        $id = explode('-', $ids);
        switch ($data['status']) {
            case 'SCORING':
                foreach ($id as $key => $target) {
                    $stop = Scoring::find($target);
                    if ($stop) {
                        $stop->update([
                            'nama' => $stop['nama'],
                            'scores' => $rating[$key],
                            'sistem' => $sistem[$key],
                            'comment' => $stop['comment'],
                            'revisi' => $stop['revisi'],
                            'assessor' => $stop['assessor'],
                            'tanggal' => ($stop['status']=='FINISHED') ? $stop['tanggal'] : $date[$key],
                            'template' => $stop['template'],
                            'status' => ($stop['status']=='FINISHED') ? 'FINISHED' : 'BEING REVIEWED',
                        ]);
                    }
                };
              break;
            case 'SUBMIT':
                $comment = explode(':Y:', $data['comment']);
                foreach ($id as $key => $target) {
                    $stop = Scoring::find($target);
                    if ($stop) {
                        $stop->update([
                            'nama' => $stop['nama'],
                            'scores' => $rating[$key],
                            'sistem' => $sistem[$key],
                            'comment' => $comment[$key],
                            'revisi' => $stop['revisi'],
                            'assessor' => $stop['assessor'],
                            'tanggal' => ($stop['status']=='FINISHED') ? $stop['tanggal'] : $date[$key],
                            'template' => $stop['template'],
                            'status' => 'FINISHED',
                        ]);
                    }
                };
              break;
            case 'REVISI':
                foreach ($id as $key => $target) {
                    $stop = Scoring::find($target);
                    if ($stop) {
                        $stop->update([
                            'nama' => $stop['nama'],
                            'scores' => $stop['scores'],
                            'sistem' => $stop['sistem'],
                            'comment' => $stop['comment'],
                            'revisi' => $rating[$key],
                            'assessor' => $stop['assessor'],
                            'tanggal' => $date[$key],
                            'template' => $stop['template'],
                            'status' => 'FINISHED',
                        ]);
                    }
                };
              break;
            default:
              //code block
          }
/*         if ($data['comment']){
            $comment = explode(':Y:', $data['comment']);
            foreach ($id as $key => $target) {
                $stop = Scoring::find($target);
                if ($stop) {
                    $stop->update([
                        'nama' => $stop['nama'],
                        'scores' => $rating[$key],
                        'sistem' => $sistem[$key],
                        'comment' => $comment[$key],
                        'revisi' => $stop['revisi'],
                        'assessor' => $stop['assessor'],
                        'tanggal' => $date[$key],
                        'template' => $stop['template'],
                        'status' => 'FINISHED',
                    ]);
                }
            };
        } else {
            foreach ($id as $key => $target) {
                $stop = Scoring::find($target);
                if ($stop) {
                    $stop->update([
                        'nama' => $stop['nama'],
                        'scores' => $rating[$key],
                        'sistem' => $sistem[$key],
                        'comment' => $stop['comment'],
                        'revisi' => $stop['revisi'],
                        'assessor' => $stop['assessor'],
                        'tanggal' => $date[$key],
                        'template' => $stop['template'],
                        'status' => 'BEING REVIEWED',
                    ]);
                }
            };
        } */

        
        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'data' => 'OK',
        ], 200);
    }

    public function startScore(ScoringRequest $request) {

        $data = $request->validated();

        $scores = Scoring::where('assessor', $data['assessor'])->where('status', 'BEING REVIEWED')
        ->update(['status' => 'PAUSED']);

/*         foreach ($scores as $score) {
            if ($score['status']=='BEING REVIEWED') {
                $scoring->update([
                    'nama' => $scoring['name'],
                    'developing' => $scoring['developing'],
                    'entrepreneurial' => $scoring['entrepreneurial'],
                    'organization' => $scoring['organization'],
                    'decision' => $scoring['decision'],
                    'thinking' => $scoring['thinking'],
                    'proactiveness' => $scoring['proactiveness'],
                    'assessor' => $scoring['assessor'],
                    'tanggal' => $scoring['tanggal'],
                    'status' => 'PAUSED',
                ]);
            }
        } */

        $target = Scoring::find($id);
        $target->update([
            'nama' => $scoring['name'],
            'scores' => $scoring['scores'],
            'template' => $scoring['template'],
            'assessor' => $scoring['assessor'],
            'tanggal' => $scoring['tanggal'],
            'status' => 'BEING REVIEWED',
        ]);

        return response()->json([
            'data' => $target,
        ]);
    }

    public function deleteData($id)
    {
        $target = Scoring::find($id);
        if (!$target) {
            return response()->json([
                'code' => 404,
                'status' => 'Not Found',
                'errors' => [
                    'message' => 'Data not found'
                ]
            ], 404);
        }
        $target->delete();

        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'message' => 'Data deleted successfully'
        ], 200);
    }

    public function updateData(ScoringRequest $request, $id)
    {
        $target = Scoring::find($id);
        if (!$target) {
            return response()->json([
                'code' => 404,
                'status' => 'Not Found',
                'errors' => [
                    'message' => 'Data not found'
                ]
            ], 404);
        }
        
        $data = $request->validated();
        $target->update($data);

        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'data' => $target
        ], 200);
    }
}
