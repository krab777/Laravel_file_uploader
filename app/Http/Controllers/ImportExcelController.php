<?php


namespace App\Http\Controllers;


use App\Imports\FileImport;
use App\Models\Item;
use App\Rules\ImportFileSize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Exceptions\NoFilePathGivenException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportExcelController extends Controller
{
    public function index()
    {
        $data = Item::with(['manufacturers', 'rubrics.parent_rubric', 'categories'])->paginate(100);
        $data->count = Item::count();

        return view('welcome', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     * @throws NoFilePathGivenException
     */
    function import(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'select_file'  => ['required', 'mimes:xls,xlsx', new ImportFileSize(1500)]
            // ImportFileSize in  kilobyte
        ]);

        $path = $request->file('select_file')->store('temp');
        $path = storage_path('app').'/'.$path;

        $headings = (new HeadingRowImport)->toArray($path);
        $headingsCount = count(array_filter($headings[0][0]));

        $countBefore = Item::count();

        $import = new FileImport($headingsCount);
        Excel::import($import, $path);

        $countAfter = Item::count();

        $wasAdded = $countAfter - $countBefore;

        $answerText = 'Total count rows: ' . $import->getRowCount()->count .'. '.'Rows with invalid data: ' . $import->getRowCount()->inwalidRows .'. '. 'Duplicated rows: ' . $import->getRowCount()->duplicatedRows .'. '. 'Was added new items: ' . $wasAdded;

        return back()->with('success', 'Excel Data Imported successfully. ' . $answerText);
    }
}
