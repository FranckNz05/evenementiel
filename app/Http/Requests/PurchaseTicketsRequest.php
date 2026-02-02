<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseTicketsRequest extends FormRequest
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
            'tickets' => [
                'required',
                'array',
                'min:1',
                'max:50' // Limite le nombre de types de billets différents
            ],
            'tickets.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:100' // Limite à 100 billets par type pour éviter les abus
            ],
            'tickets.*.ticket_id' => [
                'required',
                'integer',
                'exists:tickets,id'
            ],
            'promo_code' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/' // Seulement majuscules, chiffres et tirets
            ],
            'payment_method' => [
                'nullable',
                'string',
                'in:credit_card,paypal,stripe,yabetoo,free'
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
            'tickets.required' => 'Veuillez sélectionner au moins un billet.',
            'tickets.min' => 'Veuillez sélectionner au moins un billet.',
            'tickets.max' => 'Vous ne pouvez pas sélectionner plus de 50 types de billets différents.',
            'tickets.*.quantity.required' => 'La quantité est obligatoire.',
            'tickets.*.quantity.min' => 'La quantité doit être d\'au moins 1.',
            'tickets.*.quantity.max' => 'Vous ne pouvez pas commander plus de 100 billets du même type.',
            'tickets.*.ticket_id.required' => 'L\'identifiant du billet est obligatoire.',
            'tickets.*.ticket_id.exists' => 'Le billet sélectionné n\'existe pas.',
            'promo_code.regex' => 'Le code promo n\'est pas dans un format valide.',
            'payment_method.in' => 'La méthode de paiement n\'est pas valide.',
        ];
    }

    /**
     * Préparer les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Normaliser le code promo
        if ($this->has('promo_code') && !empty($this->promo_code)) {
            $this->merge([
                'promo_code' => strtoupper(trim($this->promo_code))
            ]);
        }
    }
}

