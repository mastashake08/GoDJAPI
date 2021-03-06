<?php

class MoodController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Request::has('name')) {
			$dj =Request::get('name');
			$moods = User::find($dj)->moods->toJson();
			return $moods;
		}
else {

		$moods = Mood::where('dj_id', '=', Auth::user()->id)->get();
		return $moods;
	}

}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//create new model
	$model = new Mood;
		//find associated DJ based on name
	$dj = User::where('username','=',Input::get('dj_id'))->get();
	$model->dj_id=$dj[0]->id;
	$model->title = Input::get('title');
	$model->requestor_name = Input::get('requestor_name');
	$model->lat = Input::get('lat');
        $model->long = Input::get('long');
        if($model->save())
        {

 //Send message to DJ
Larapush::send(['message' => $model->toJson()], [$dj[0]->username], 'mood.request');
Larapush::send(['message' => $model->requestor_name.' has sent a mood request of ' .$model->title. ' to DJ '. $dj[0]->username], ['demo'], 'generic.event');


        }
        else
        {

        return Response::json(array(
        'error' => true,
        'message' => 'There was an error. Please try again.'),
        400
        );

        }

}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	$model = Mood::findOrFail($id);
	return Response::json(array(
	'error' => false,
	'mood' => $model,
	200
	);
	}
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	$model = Mood::findOrFail($id);
	if($dj=Input::get('dj_id'))
	{
	$model->dj_id = $dj;
	}
	if($title = Input::get('title'))
	{
	$model->title = $title;
	}
	if($requestor = Input::get('requestor_name'))
	{
	$model->requestor_name = $requestor;
	}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	$model = Mood::findOrFail($id);
	if($model->delete())
	{
	return Response::json(array(
	'error' => false,
	'message' => 'Input successfully deleted'),
	200
	);
	}
	else
	{
	return Response::json(array(
	'error' => true,
	'message' => 'There was an error processing your Input. Please try again.'),
	400
	);
	}
	}


}
