<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'min:2',
                'max:1000',
                // Bloquer les balises script et autres contenus dangereux
                'regex:/^(?!.*<script).*$/i'
            ],
            'blog_id' => [
                'required_without:event_id',
                'nullable',
                'integer',
                'exists:blogs,id'
            ],
            'event_id' => [
                'required_without:blog_id',
                'nullable',
                'integer',
                'exists:events,id'
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:comments,id'
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
            'content.required' => 'Le commentaire ne peut pas être vide.',
            'content.min' => 'Le commentaire doit contenir au moins 2 caractères.',
            'content.max' => 'Le commentaire ne doit pas dépasser 1000 caractères.',
            'content.regex' => 'Le commentaire contient du contenu non autorisé.',
            'blog_id.required_without' => 'Un blog ou un événement doit être spécifié.',
            'blog_id.exists' => 'Le blog n\'existe pas.',
            'event_id.required_without' => 'Un blog ou un événement doit être spécifié.',
            'event_id.exists' => 'L\'événement n\'existe pas.',
            'parent_id.exists' => 'Le commentaire parent n\'existe pas.',
        ];
    }

    /**
     * Préparer les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le contenu en supprimant tous les tags HTML
        if ($this->has('content')) {
            $this->merge([
                'content' => strip_tags($this->content)
            ]);
        }
    }
}

