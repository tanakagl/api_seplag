import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import axios from 'axios';

export default function RenovaToken() {
    const { auth } = usePage().props as any;
    const [showRenewalPrompt, setShowRenewalPrompt] = useState(false);
    const [timeLeft, setTimeLeft] = useState<number | null>(null);
    const [isRenewing, setIsRenewing] = useState(false);

    useEffect(() => {
        // Não fazer nada se não houver informações de autenticação
        if (!auth || !auth.token_expires_in_seconds) {
            return;
        }

        const checkTokenExpiration = () => {
            const secondsRemaining = auth.token_expires_in_seconds;
            
            // Atualizar o tempo restante
            setTimeLeft(secondsRemaining);
            
            // Mostrar prompt quando faltar menos de 1 minuto
            if (secondsRemaining > 0 && secondsRemaining < 60) {
                setShowRenewalPrompt(true);
            }
            
            // Também mostrar se já expirou
            if (secondsRemaining <= 0) {
                setShowRenewalPrompt(true);
            }
        };

        // Verificar imediatamente e depois a cada 10 segundos
        checkTokenExpiration();
        const interval = setInterval(checkTokenExpiration, 10000);
        
        return () => clearInterval(interval);
    }, [auth]);

    const renewToken = async () => {
        try {
            setIsRenewing(true);
            
            // Chamar a API para renovar o token
            const response = await axios.post('/api/refresh-token');
            
            // Atualizar o token na sessão
            await axios.post('/update-session-token', {
                token: response.data.token,
                expires_at: response.data.expires_at
            });
            
            // Recarregar a página para atualizar as props
            window.location.reload();
            setShowRenewalPrompt(false);
        } catch (error) {
            console.error('Erro ao renovar token:', error);
            // Redirecionar para login em caso de erro
            window.location.href = '/login';
        } finally {
            setIsRenewing(false);
        }
    };

    const logout = () => {
        window.location.href = '/logout';
    };

    if (!showRenewalPrompt) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg shadow-lg max-w-md dark:bg-gray-800 dark:text-white">
                <h2 className="text-xl font-bold mb-4">
                    {timeLeft && timeLeft > 0
                        ? 'Sua sessão está prestes a expirar'
                        : 'Sua sessão expirou'}
                </h2>
                <p className="mb-6">
                    {timeLeft && timeLeft > 0
                        ? `Sua sessão expirará em ${timeLeft} segundos. Deseja continuar conectado?`
                        : 'Deseja renovar sua sessão para continuar usando o sistema?'}
                </p>
                <div className="flex justify-end space-x-4">
                    <button
                        onClick={logout}
                        className="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600"
                        disabled={isRenewing}
                    >
                        Sair
                    </button>
                    <button
                        onClick={renewToken}
                        className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        disabled={isRenewing}
                    >
                        {isRenewing ? 'Renovando...' : 'Continuar'}
                    </button>
                </div>
            </div>
        </div>
    );
}
