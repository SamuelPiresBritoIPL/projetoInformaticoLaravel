<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CadeiraPostRequest extends FormRequest
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
        if ($this->isMethod('put')) {
            $rules = array_merge($rules,array('inscricaoIds' => ['required', 'exists:inscricao,id']));

        }else{
            if($this->request->has('email')){
                $rules = array_merge($rules,array('email' => ['required', Rule::exists('utilizador', 'email')->where('email', $this->request->get('email'))]));
            }else{
                if($this->request->has('login')){
                    if(str_contains($this->request->get('login'), '@')){
                        $rules = array_merge($rules,array('login' => ['required', Rule::exists('utilizador', 'email')->where('email', $this->request->get('login'))]));
                    }else{
                        $rules = array_merge($rules,array('login' => ['required', Rule::exists('utilizador', 'login')->where('login', $this->request->get('login'))]));
                    }
                }else{
                    $rules = array_merge($rules,array('login' => ['required', Rule::exists('utilizador', 'login')->where('login', $this->request->get('login'))]));
                }
            }  
        }
        return $rules;
    }

    public function messages()
    {
        return array_merge(
            parent::messages(),
            [
                'login.required'=>'Não defeniu o login.',
                'login.exists'=>'O login definido não existe.',
                'email.required'=>'Não defeniu o email.',
                'email.exists'=>'O email definido não existe.',
                'inscricaoIds.required'=>'O turno presente não tem alunos inscritos para se moverem.',
                'inscricaoIds.exists'=>'Os turnos selecionados não existem.',
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
