<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PedidosPostRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            $this->validate([
                'estado' => ['required', 'in:0,1'],
            ]);
            //'idUtilizador', 'idAnoletivo', 'estado', 'descricao'
            $rules = [
                'descricao' => ['required', 'string', 'max:255'],
                'estado' => ['required', 'in:0,1'],
                'semestre' => ['required', 'in:1,2'],
                'idUtilizador' => ['required', 'numeric', Rule::exists('utilizador', 'id')->where('id', $this->request->get('idUtilizador'))],
                'idAnoletivo' => ['required', 'numeric', Rule::exists('anoletivo', 'id')->where('id', $this->request->get('idAnoletivo'))],
            ];
            
            //tem de receber um array de ids de cadeiras
            if($this->request->get('estado') == 1){
                $rules = array_merge($rules,array('cadeirasIds' => ['required','exists:cadeira,id']));
            }
        }

        if($this->isMethod('get')){
            $rules = [
                'estado' => ['required', 'in:0,1,2,3,4'],
                'semestre' => ['required', 'in:1,2'],
                'idAnoletivo' => ['required', 'numeric', Rule::exists('anoletivo', 'id')->where('id', $this->request->get('idAnoletivo'))],
            ];
        }

        if($this->isMethod('put')){
            $rules = [
                'pedidosucsAprovadasIds' => ['','exists:pedidosucs,id'],
                'pedidosucsReprovadasIds' => ['','exists:pedidosucs,id']
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return array_merge(
            parent::messages(),
            [
                'descricao.required'=>'Tem de justificar o porquê do seu pedido.',
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
