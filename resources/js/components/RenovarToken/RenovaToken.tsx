import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import axios from 'axios';

export default function RenovaToken() {
  const { auth } = usePage().props;
  const [showRenewalPrompt, setShowRenewalPrompt] = useState(false);
  const [timeLeft, setTimeLeft] = useState<number | null>(null);
  
  useEffect(() => {
    const checkTokenExpiration = () => {
      // @ts-ignore
      const tokenExpiresAt = auth?.token_expires_at;
      
      if (tokenExpiresAt) {
        const expirationTime = new Date(tokenExpiresAt).getTime();
        const currentTime = new Date().getTime();
        const timeRemaining = expirationTime - currentTime;
        
        setTimeLeft(Math.floor(timeRemaining / 1000));
        
        if (timeRemaining > 0 && timeRemaining < 60000) {
          setShowRenewalPrompt(true);
        }
        
        if (timeRemaining <= 0) {
          setShowRenewalPrompt(true);
        }
      }
    };
    
    const interval = setInterval(checkTokenExpiration, 10000);
    
    checkTokenExpiration();
    
    return () => clearInterval(interval);
  }, [auth]);
  
  const renewToken = async () => {
    try {
      const response = await axios.post('/api/refresh-token');
      
      await axios.post('/update-session-token', {
        token: response.data.token,
        expires_at: response.data.expires_at
      });
      
      window.location.reload();
      setShowRenewalPrompt(false);
    } catch (error) {
      window.location.href = '/login';
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
          >
            Sair
          </button>
          <button
            onClick={renewToken}
            className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Continuar
          </button>
        </div>
      </div>
    </div>
  );
}