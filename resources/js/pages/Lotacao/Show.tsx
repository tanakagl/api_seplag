import React from 'react'
import { Head, Link } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Pessoa {
    pes_id: number;
    pes_nome: string;
    pes_data_nascimento?: string;
    pes_sexo?: string;
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

interface ShowProps extends PageProps {
    lotacao: Lotacao;
}

export default function Show({ lotacao }: ShowProps) {
    const formatDate = (dateString: string | null) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('pt-BR');
    };

    return (
        <>
            <Head title={`Lotação: ${lotacao.pessoa.pes_nome}`} />

            <div className="container mx-auto py-8">
                <div className="max-w-4xl mx-auto">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold">Detalhes da Lotação</h1>
                        <div className="flex space-x-4">
                            <Link
                                href={route('lotacao.edit', lotacao.lot_id)}
                                className="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded"
                            >
                                Editar
                            </Link>
                            <Link
                                href={route('lotacao.index')}
                                className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                            >
                                Voltar
                            </Link>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-xl font-semibold">Informações da Lotação</h2>
                        </div>
                        <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Servidor:</p>
                                <p className="font-medium">{lotacao.pessoa.pes_nome}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Unidade:</p>
                                <p className="font-medium">{lotacao.unidade.unid_nome} ({lotacao.unidade.unid_sigla})</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Lotação:</p>
                                <p className="font-medium">{formatDate(lotacao.lot_data_lotacao)}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Remoção:</p>
                                <p className="font-medium">{formatDate(lotacao.lot_data_remocao)}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Portaria:</p>
                                <p className="font-medium">{lotacao.lot_portaria || '-'}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Status:</p>
                                <p className="font-medium">
                                    <span className={`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${!lotacao.lot_data_remocao ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                                        {!lotacao.lot_data_remocao ? 'Ativa' : 'Inativa'}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-xl font-semibold">Informações do Servidor</h2>
                        </div>
                        <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome:</p>
                                <p className="font-medium">{lotacao.pessoa.pes_nome}</p>
                            </div>
                            {lotacao.pessoa.pes_data_nascimento && (
                                <div>
                                    <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Nascimento:</p>
                                    <p className="font-medium">{formatDate(lotacao.pessoa.pes_data_nascimento)}</p>
                                </div>
                            )}
                            {lotacao.pessoa.pes_sexo && (
                                <div>
                                    <p className="text-gray-500 dark:text-gray-400 text-sm">Sexo:</p>
                                    <p className="font-medium">{lotacao.pessoa.pes_sexo === 'M' ? 'Masculino' : 'Feminino'}</p>
                                </div>
                            )}
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Ações:</p>
                                <Link
                                    href={route('servidores.efetivo.show', lotacao.pessoa.pes_id)}
                                    className="text-blue-500 hover:text-blue-700"
                                >
                                    Ver detalhes do servidor
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
