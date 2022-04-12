<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CoordenadorPostRequest extends FormRequest
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
        $rules = [
			'tipo' => ['required', 'in:1,0'],
			'idCurso' => ['required', 'numeric', Rule::exists('curso', 'id')->where('id', $this->request->get('idCurso'))],
		];

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
        return $rules;
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
