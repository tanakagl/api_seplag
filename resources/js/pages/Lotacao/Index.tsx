import React from 'react'
import { Head, Link, router } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Pessoa {
    pes_id: number;
    pes_nome: string;
}

interface Unidade {
    unid_id: number;
    unid_nome: string;
    unid_sigla: string;
}

interface Lotacao {
    lot_id: number;
    pes_id: number;
    unid_id: number;
    lot_data_lotacao: string;
    lot_data_remocao: string | null;
    lot_portaria: string | null;
    pessoa: Pessoa;
    unidade: Unidade;
}

interface LotacaoIndexProps extends PageProps {
    lotacoes: {
        data: Lotacao[];
        links: any;
        current_page: number;
        last_page: number;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}

export default function Index({ lotacoes, flash }: LotacaoIndexProps) {
    const handleDelete = (lotId: number) => {
        if (confirm('Tem certeza que deseja excluir esta lotação?')) {
            router.delete(route('lotacao.destroy', lotId));
        }
    };

    const formatDate = (dateString: string | null) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('pt-BR');
    };

    return (
        <>
            <Head title="Lotações" />

            <div className="container mx-auto py-8">
                <div className="flex justify-between items-center mb-6">
                    <div className="flex items-center space-x-4">
                        <Link
                            href={route('welcome')}
                            className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                        >
                            Voltar ao Início
                        </Link>
                        <h1 className="text-2xl font-semibold">Lotações</h1>
                    </div>
                    <Link
                        href={route('lotacao.create')}
                        className="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded"
                    >
                        Adicionar Lotação
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
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Servidor</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unidade</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Lotação</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Remoção</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Portaria</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {lotacoes.data.map((lotacao) => (
                                <tr key={lotacao.lot_id}>
                                    <td className="px-6 py-4 whitespace-nowrap">{lotacao.pessoa.pes_nome}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {lotacao.unidade.unid_nome} ({lotacao.unidade.unid_sigla})
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {formatDate(lotacao.lot_data_lotacao)}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {formatDate(lotacao.lot_data_remocao)}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {lotacao.lot_portaria || '-'}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            href={route('lotacao.show', lotacao.lot_id)}
                                            className="text-blue-500 hover:text-blue-700 mr-4"
                                        >
                                            Ver
                                        </Link>
                                        <Link
                                            href={route('lotacao.edit', lotacao.lot_id)}
                                            className="text-yellow-500 hover:text-yellow-700 mr-4"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(lotacao.lot_id)}
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
                        {lotacoes.links && (
                            <div className="flex justify-between">
                                {lotacoes.current_page > 1 && (
                                    <Link
                                        href={route('lotacao.index', { page: lotacoes.current_page - 1 })}
                                        className="text-blue-500"
                                    >
                                        Anterior
                                    </Link>
                                )}

                                <span>
                                    Página {lotacoes.current_page} de {lotacoes.last_page}
                                </span>

                                {lotacoes.current_page < lotacoes.last_page && (
                                    <Link
                                        href={route('lotacao.index', { page: lotacoes.current_page + 1 })}
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
