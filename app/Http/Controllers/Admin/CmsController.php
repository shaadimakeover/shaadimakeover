<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\FileHelperTrait;

class CmsController extends Controller
{
    use FileHelperTrait;
    /**
     * function details
     * @date date
     * @param
     * @return view
     * @author Gourab
     */
    public function index(Request $request)
    {
        try {
            return view('admin.cms.list');
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            //return redirect()->back()->with('error', "Something went wrong, please try again!");
            abort(500);
        }
    }


    /**
     * function details
     * @date date
     * @param
     * @return view
     * @author Gourab
     */
    public function edit($id)
    {
        try {
            $cms = Cms::find($id);
            return view('admin.cms.create-edit', compact('cms'));
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            //return redirect()->back()->with('error', "Something went wrong, please try again!");
            abort(500);
        }
    }


    /**
     * function details
     * @date date
     * @param
     * @return view
     * @author Gourab
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|max:150',
            'text_content' => 'required',
        ];
        $this->validate(request(), $rules);

        try {
            $page = Cms::where('id', $id)->first();
            $page->title = $request->title;
            $page->text_content = $request->text_content;
            $page->save();

            return redirect()->route('cms.index')->with('success', $page->title . ' has been updated');
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            //return redirect()->back()->with('error', "Something went wrong, please try again!");
            abort(500);
        }
    }
}
