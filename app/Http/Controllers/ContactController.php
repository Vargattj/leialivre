<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Exibe a página de contato
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Processa o envio do formulário de contato
     * (Implementação futura)
     */
    public function store(Request $request)
    {
        // Validação dos campos do formulário
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|in:sugestao,problema,parceria,duvida,outro',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'subject.required' => 'Por favor, selecione um assunto.',
            'subject.in' => 'O assunto selecionado é inválido.',
            'message.required' => 'O campo mensagem é obrigatório.',
            'message.min' => 'A mensagem deve ter pelo menos 10 caracteres.',
            'message.max' => 'A mensagem não pode ter mais de 5000 caracteres.',
        ]);

        // TODO: Implementar captura e processamento do formulário
        // Exemplos de implementação futura:
        // - Salvar no banco de dados
        // - Enviar email de notificação
        // - Integrar com serviço de email (Mailgun, SendGrid, etc.)
        // - Adicionar proteção contra spam (reCAPTCHA)
        
        // Por enquanto, apenas retorna com mensagem de sucesso
        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}

