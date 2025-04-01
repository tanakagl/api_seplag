import React from 'react'
import { Head, Link } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Pessoa {
    pes_id: number;
    pes_nome: string;
}

interface Lotacao {
    lot_id: number;
    pes_id: number;
    unid_id: number;
    data_inicio: string;
    data_fim: string | null;
    pessoa: Pessoa;
}

interface Unidade {
    unid_id: number;
    unid_nome: string;
    unid_sigla: string;
    lotacao?: Lotacao[];
}

interface ShowProps extends PageProps {
    unidade: Unidade;
}

export default function Show({ unidade }: ShowProps) {
    return (
        <>
            <Head title={`Unidade: ${unidade.unid_nome}`} />

            <div className="container mx-auto py-8">
                <div className="max-w-4xl mx-auto">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold">Detalhes da Unidade</h1>
                        <div className="flex space-x-4">
                            <Link
                                href={route('unidade.edit', unidade.unid_id)}
                                className="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded"
                            >
                                Editar
                            </Link>
                            <Link
                                href={route('unidade.index')}
                                className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                            >
                                Voltar
                            </Link>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-xl font-semibold">Informações da Unidade</h2>
                        </div>
                        <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome:</p>
                                <p className="font-medium">{unidade.unid_nome}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Sigla:</p>
                                <p className="font-medium">{unidade.unid_sigla}</p>
                            </div>
                        </div>
                    </div>

                    {unidade.lotacao && unidade.lotacao.length > 0 && (
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 className="text-xl font-semibold">Servidores Lotados</h2>
                            </div>
                            <div className="p-6">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Início</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Fim</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        {unidade.lotacao.map((lotacao) => (
                                            <tr key={lotacao.lot_id}>
                                                <td className="px-6 py-4 whitespace-nowrap">{lotacao.pessoa.pes_nome}</td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    {new Date(lotacao.data_inicio).toLocaleDateString('pt-BR')}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    {lotacao.data_fim 
                                                        ? new Date(lotacao.data_fim).toLocaleDateString('pt-BR') 
                                                        : 'Atual'}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
