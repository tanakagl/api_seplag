import { type SharedData } from '@/types';
import { Head, usePage, Link } from '@inertiajs/react';
import { useState } from 'react';
import { router } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;
    const [isLoading, setIsLoading] = useState(false);

    // Função para navegar para as diferentes páginas de CRUD usando o router do Inertia
    const navigateTo = (route: string) => {
        setIsLoading(true);
        router.visit(route);
    };

    return (
        <>
            <Head title="SEPLAG - Sistema de Gestão">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a] dark:text-white">
                <header className="mb-12 w-full max-w-4xl text-center">
                    <h1 className="text-4xl font-bold mb-2">SEPLAG</h1>
                    <p className="text-lg text-gray-600 dark:text-gray-300">Sistema de Gestão de Pessoal</p>
                </header>
                <main className="w-full max-w-4xl">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                        <h2 className="text-2xl font-semibold mb-6 text-center">Módulos do Sistema</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <button
                                onClick={() => navigateTo(route('servidores.efetivo.index'))}
                                className="bg-blue-600 hover:bg-blue-700 text-white py-4 px-6 rounded-lg shadow transition-colors duration-200 flex flex-col items-center justify-center"
                                disabled={isLoading}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span className="text-lg font-medium">Servidores Efetivos</span>
                            </button>
                            <button
                                onClick={() => navigateTo(route('servidores.temporario.index'))}
                                className="bg-green-600 hover:bg-green-700 text-white py-4 px-6 rounded-lg shadow transition-colors duration-200 flex flex-col items-center justify-center"
                                disabled={isLoading}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span className="text-lg font-medium">Servidores Temporários</span>
                            </button>
                            <button
                                onClick={() => navigateTo(route('unidade.index'))}
                                className="bg-purple-600 hover:bg-purple-700 text-white py-4 px-6 rounded-lg shadow transition-colors duration-200 flex flex-col items-center justify-center"
                                disabled={isLoading}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span className="text-lg font-medium">Unidade</span>
                            </button>
                            <button
                                onClick={() => navigateTo(route('lotacao.index'))} 
                                className="bg-amber-600 hover:bg-amber-700 text-white py-4 px-6 rounded-lg shadow transition-colors duration-200 flex flex-col items-center justify-center"
                                disabled={isLoading}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span className="text-lg font-medium">Lotações</span>
                            </button>
                        </div>
                    </div>
                    {auth.user && (
                        <div className="mt-8 text-center">
                            <p className="text-gray-600 dark:text-gray-300">
                                Logado como <span className="font-semibold">{auth.user.name}</span>
                            </p>
                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                className="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 mt-2 inline-block"
                            >
                                Sair do Sistema
                            </Link>
                        </div>
                    )}
                </main>
                <footer className="mt-12 text-center text-gray-500 dark:text-gray-400 text-sm">
                    <p>© {new Date().getFullYear()} SEPLAG - Todos os direitos reservados</p>
                </footer>
            </div>
        </>
    );
}
