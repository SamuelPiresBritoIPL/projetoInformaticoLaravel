<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AberturaPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if ($this->isMethod('post')) {
            $rules = [
                'dataAbertura' => ['required', 'date'],
                'dataEncerar' => ['required', 'date','after_or_equal:dataAbertura'],
                'ano' => ['required', 'in:0,1,2,3,4,5'],
                'tipoAbertura' => ['required', 'in:0,1'],
                'semestre' => ['required', 'in:1,2'],
                'idAnoletivo' => ['required', 'numeric', Rule::exists('anoletivo', 'id')->where('id', $this->request->get('idAnoletivo'))],
            ];
        }else{
            if(request()->has("dataAbertura") || request()->has("dataEncerar")){
                $rules = array_merge($rules,array('dataAbertura' => ['required', 'date'],
                                                'dataEncerar' => ['required', 'date','after_or_equal:dataAbertura']));
            }
            if(request()->has("ano")){
                $rules = array_merge($rules,array('ano' => ['required', 'in:0,1,2,3,4,5']));
            }
        }

        return $rules;
    }

    public function messages()
    {
        return array_merge(
            parent::messages(),
            [
                'dataAbertura.required'=>'Não selecionou a data de abertura.',
                'dataEncerar.required'=>'Não selecionou a data de encerrar.',
                'ano.required'=>'Não selecionou o ano.',
                'semestre.required'=>'O semestre não foi selecionado.',
                'idAnoletivo.required'=>'O ano letivo não foi selecionado.',
                'dataEncerar.after_or_equal'=>'A data de encerrar não pode ser igual ao inferior à data de abertura.',
                'ano.in'=>'Não selecionou o ano.'
            ]
        );
    }

    /**
    * [failedValidation [Overriding the event validator for custom error response]]
    * @param  Validator $validator [description]
    * @return [object][object of various validation errors]
    */
    public function failedValidation(Validator $validator) { 
        //write your bussiness logic here otherwise it will give same old JSON response
       throw new HttpResponseException(response()->json($validator->errors(), 422)); 
   }
}
