import { useEffect, useState } from 'react';
import axios from 'axios';
import { Button } from '../ui/button';

function RenovarToken() {
    const [showRenewalPrompt, setShowRenewalPrompt] = useState(false);
    
    useEffect(() => {
        const checkTokenExpiration = () => {
            const tokenExpiration = localStorage.getItem('token_expiration');
            
            if (tokenExpiration) {
                const expirationTime = new Date(tokenExpiration);
                const currentTime = new Date();
                
                // Se faltam menos de 1 minuto para expirar
                if ((expirationTime.getTime() - currentTime.getTime()) < 60000) {
                    setShowRenewalPrompt(true);
                }
            }
        };
        
        // Verificar a cada 30 segundos
        const interval = setInterval(checkTokenExpiration, 30000);
        
        return () => clearInterval(interval);
    }, []);
    
    const renewToken = async () => {
        try {
            const response = await axios.post('/api/refresh-token');
            
            localStorage.setItem('api_token', response.data.token);
            localStorage.setItem('token_expiration', new Date(Date.now() + 5 * 60000).toISOString());
            
            setShowRenewalPrompt(false);
        } catch (error) {
            console.error('Erro ao renovar token:', error);
            window.location.href = '/login';
        }
    };
    
    const logout = () => {
        axios.post('/api/logout').then(() => {
            localStorage.removeItem('api_token');
            localStorage.removeItem('token_expiration');
            window.location.href = '/login';
        });
    };
    
    if (!showRenewalPrompt) return null;
    
    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg shadow-lg max-w-md">
                <h2 className="text-xl font-bold mb-4">Sua sessão está prestes a expirar</h2>
                <p className="mb-6">Deseja continuar conectado?</p>
                <div className="flex justify-end space-x-4">
                    <Button 
                        onClick={logout}
                        className="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                    >
                        Sair
                    </Button>
                    <Button 
                        onClick={renewToken}
                        className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Continuar
                    </Button>
                </div>
            </div>
        </div>
    );
}
export default RenovarToken;
