import React from 'react'
import { Head, Link, router } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Cidade {
    cid_id: number;
    cid_nome: string;
    estado_id: number;
}

interface Endereco {
    end_id: number;
    end_tipo_logradouro: string;
    end_logradouro: string;
    end_numero: number | null;
    end_bairro: string;
    cid_id: number;
    cidade: Cidade;
}

interface EnderecoIndexProps extends PageProps {
    enderecos: {
        data: Endereco[];
        links: any;
        current_page: number;
        last_page: number;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}

export default function Index({ enderecos, flash }: EnderecoIndexProps) {
    const handleDelete = (endId: number) => {
        if (confirm('Tem certeza que deseja excluir este endereço?')) {
            router.delete(route('endereco.destroy', endId), {
                onSuccess: () => {
                    console.log('Endereço excluído com sucesso');
                },
                onError: (errors) => {
                    console.error('Erro ao excluir endereço:', errors);
                }
            });
        }
    };

    return (
        <>
            <Head title="Endereços" />

            <div className="container mx-auto py-8">
                <div className="flex justify-between items-center mb-6">
                    <div className="flex items-center space-x-4">
                        <Link
                            href={route('welcome')}
                            className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                        >
                            Voltar ao Início
                        </Link>
                        <h1 className="text-2xl font-semibold">Endereços</h1>
                    </div>
                    <Link
                        href={route('endereco.create')}
                        className="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded"
                    >
                        Adicionar Endereço
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
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Logradouro</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Número</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bairro</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cidade</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {enderecos.data.map((endereco) => (
                                <tr key={endereco.end_id}>
                                    <td className="px-6 py-4 whitespace-nowrap">{endereco.end_tipo_logradouro}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">{endereco.end_logradouro}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">{endereco.end_numero || 'S/N'}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">{endereco.end_bairro}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">{endereco.cidade?.cid_nome || 'Não informada'}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            href={route('endereco.show', endereco.end_id)}
                                            className="text-blue-500 hover:text-blue-700 mr-4"
                                        >
                                            Ver
                                        </Link>
                                        <Link
                                            href={route('endereco.edit', endereco.end_id)}
                                            className="text-yellow-500 hover:text-yellow-700 mr-4"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(endereco.end_id)}
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
                        {enderecos.links && (
                            <div className="flex justify-between">
                                {enderecos.current_page > 1 && (
                                    <Link
                                        href={route('endereco.index', { page: enderecos.current_page - 1 })}
                                        className="text-blue-500"
                                    >
                                        Anterior
                                    </Link>
                                )}

                                <span>
                                    Página {enderecos.current_page} de {enderecos.last_page}
                                </span>

                                {enderecos.current_page < enderecos.last_page && (
                                    <Link
                                        href={route('endereco.index', { page: enderecos.current_page + 1 })}
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
