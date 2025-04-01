import React from 'react'
import { Head, Link } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Pessoa {
    pes_id: number;
    pes_nome: string;
}

interface Cidade {
    cid_id: number;
    nome: string;
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
    pessoas: Pessoa[];
}

interface ShowProps extends PageProps {
    endereco: Endereco;
}

export default function Show({ endereco }: ShowProps) {
    return (
        <>
            <Head title={`Endereço: ${endereco.end_logradouro}`} />

            <div className="container mx-auto py-8">
                <div className="max-w-4xl mx-auto">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold">Detalhes do Endereço</h1>
                        <div className="flex space-x-4">
                            <Link
                                href={route('endereco.edit', endereco.end_id)}
                                className="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded"
                            >
                                Editar
                            </Link>
                            <Link
                                href={route('endereco.index')}
                                className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                            >
                                Voltar
                            </Link>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-xl font-semibold">Informações do Endereço</h2>
                        </div>
                        <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Tipo de Logradouro:</p>
                                <p className="font-medium">{endereco.end_tipo_logradouro}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Logradouro:</p>
                                <p className="font-medium">{endereco.end_logradouro}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Número:</p>
                                <p className="font-medium">{endereco.end_numero || 'S/N'}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Bairro:</p>
                                <p className="font-medium">{endereco.end_bairro}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Cidade:</p>
                                <p className="font-medium">{endereco.cidade?.nome || 'Não informada'}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Endereço Completo:</p>
                                <p className="font-medium">
                                    {endereco.end_tipo_logradouro} {endereco.end_logradouro}, {endereco.end_numero || 'S/N'} - {endereco.end_bairro}, {endereco.cidade?.nome || 'Cidade não informada'}
                                </p>
                            </div>
                        </div>
                    </div>

                    {endereco.pessoas && endereco.pessoas.length > 0 && (
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 className="text-xl font-semibold">Pessoas Associadas</h2>
                            </div>
                            <div className="p-6">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        {endereco.pessoas.map((pessoa) => (
                                            <tr key={pessoa.pes_id}>
                                                <td className="px-6 py-4 whitespace-nowrap">{pessoa.pes_nome}</td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <Link
                                                        href={route('pessoa.endereco.remove', [pessoa.pes_id, endereco.end_id])}
                                                        method="delete"
                                                        as="button"
                                                        className="text-red-500 hover:text-red-700"
                                                    >
                                                        Remover
                                                    </Link>
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

