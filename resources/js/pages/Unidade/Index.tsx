import React from 'react'
import { Head, Link, router } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Unidade {
    unid_id: number;
    unid_nome: string;
    unid_sigla: string;
}

interface UnidadeIndexProps extends PageProps {
    unidade: {
        data: Unidade[];
        links: any;
        current_page: number;
        last_page: number;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}

export default function Index({ unidade, flash }: UnidadeIndexProps) {
    const handleDelete = (uniId: number) => {
        if (confirm('Tem certeza que deseja excluir esta unidade?')) {
            router.delete(route('unidade.destroy', uniId), {
                onSuccess: () => {
                    console.log('Unidade excluída com sucesso');
                },
                onError: (errors) => {
                    console.error('Erro ao excluir unidade:', errors);
                }
            });
        }
    };

    return (
        <>
            <Head title="Unidades" />

            <div className="container mx-auto py-8">
                <div className="flex justify-between items-center mb-6">
                    <div className="flex items-center space-x-4">
                        <Link
                            href={route('welcome')}
                            className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                        >
                            Voltar ao Início
                        </Link>
                        <h1 className="text-2xl font-semibold">unidade</h1>
                    </div>
                    <Link
                        href={route('unidade.create')}
                        className="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded"
                    >
                        Adicionar Unidade
                    </Link>
                </div>

                {flash?.success && (
                    <div className="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                        {flash.success}
                    </div>
                )}

                {flash?.error && (
                    <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        {flash.error}
                    </div>
                )}

                <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead className="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sigla</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {unidade.data.map((unidade) => (
                                <tr key={unidade.unid_id}>
                                    <td className="px-6 py-4 whitespace-nowrap">{unidade.unid_nome}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">{unidade.unid_sigla}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            href={route('unidade.show', unidade.unid_id)}
                                            className="text-blue-500 hover:text-blue-700 mr-4"
                                        >
                                            Ver
                                        </Link>
                                        <Link
                                            href={route('unidade.edit', unidade.unid_id)}
                                            className="text-yellow-500 hover:text-yellow-700 mr-4"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(unidade.unid_id)}
                                            className="text-red-500 hover:text-red-700"
                                        >
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    <div className="px-6 py-4">
                        {unidade.links && (
                            <div className="flex justify-between">
                                {unidade.current_page > 1 && (
                                    <Link
                                        href={route('unidade.index', { page: unidade.current_page - 1 })}
                                        className="text-blue-500"
                                    >
                                        Anterior
                                    </Link>
                                )}

                                <span>
                                    Página {unidade.current_page} de {unidade.last_page}
                                </span>

                                {unidade.current_page < unidade.last_page && (
                                    <Link
                                        href={route('unidade.index', { page: unidade.current_page + 1 })}
                                        className="text-blue-500"
                                    >
                                        Próxima
                                    </Link>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
