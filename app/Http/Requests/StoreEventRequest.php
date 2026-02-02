<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && 
               (auth()->user()->hasRole('Organizer') || auth()->user()->hasRole('Administrateur'));
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
            'description' => [
                'required',
                'string',
                'max:10000'
            ],
            'start_date' => [
                'required',
                'date',
                'after:now'
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date'
            ],
            'ville' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ]+$/'
            ],
            'pays' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ]+$/'
            ],
            'adresse' => [
                'required',
                'string',
                'max:255'
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // 5MB max pour les images d'événements
                'dimensions:min_width=300,min_height=200,max_width=5000,max_height=5000'
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id'
            ],
            'event_type' => [
                'required',
                'string',
                'in:avec places,libre'
            ],
            'status' => [
                'required',
                'string',
                'in:Gratuit,Payant'
            ],
            'keywords' => [
                'nullable',
                'string',
                'max:500'
            ],
            'adresse_map' => [
                'nullable',
                'string',
                'max:2000'
            ],
            'capacite_totale' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000000'
            ],
            'video_url' => [
                'nullable',
                'url',
                'max:500'
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
            'title.required' => 'Le titre de l\'événement est obligatoire.',
            'title.regex' => 'Le titre contient des caractères non autorisés.',
            'description.required' => 'La description est obligatoire.',
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.after' => 'La date de début doit être dans le futur.',
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'ville.required' => 'La ville est obligatoire.',
            'ville.regex' => 'La ville contient des caractères non autorisés.',
            'pays.required' => 'Le pays est obligatoire.',
            'pays.regex' => 'Le pays contient des caractères non autorisés.',
            'image.max' => 'L\'image ne doit pas dépasser 5MB.',
            'category_id.required' => 'La catégorie est obligatoire.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'event_type.in' => 'Le type d\'événement n\'est pas valide.',
            'status.in' => 'Le statut n\'est pas valide.',
        ];
    }

    /**
     * Préparer les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer les entrées sensibles
        if ($this->has('title')) {
            $this->merge([
                'title' => strip_tags($this->title)
            ]);
        }
        
        if ($this->has('keywords')) {
            $this->merge([
                'keywords' => strip_tags($this->keywords)
            ]);
        }
    }
}

