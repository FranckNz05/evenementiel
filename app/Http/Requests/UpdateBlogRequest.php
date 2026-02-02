<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $blog = $this->route('blog');
        return auth()->check() && 
               (auth()->user()->can('update', $blog) || 
                auth()->user()->hasRole('Administrateur'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\.,!?àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ]+$/'
            ],
            'content' => [
                'required',
                'string',
                'max:50000'
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id'
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:160'
            ],
            'meta_keywords' => [
                'nullable',
                'string',
                'max:255'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'title.regex' => 'Le titre contient des caractères non autorisés.',
            'content.required' => 'Le contenu est obligatoire.',
            'content.max' => 'Le contenu est trop long.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format jpeg, png, jpg ou webp.',
            'image.max' => 'L\'image ne doit pas dépasser 2MB.',
            'image.dimensions' => 'Les dimensions de l\'image ne sont pas valides.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
        ];
    }

    /**
     * Préparer les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le titre
        if ($this->has('title')) {
            $this->merge([
                'title' => strip_tags($this->title)
            ]);
        }
    }
}

