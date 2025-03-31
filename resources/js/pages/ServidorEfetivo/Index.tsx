import React from 'react'
import { Head, Link, router } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Pessoa {
    pes_id: number;
    pes_nome: string;
    pes_data_nascimento: string;
    pes_sexo: string;
    pes_mae: string;
    pes_pai: string;
}

interface ServidorEfetivo {
    pes_id: number;
    se_matricula: string;
    pessoa: Pessoa;
}

interface ServidorEfetivoIndexProps extends PageProps {
    servidores: {
        data: ServidorEfetivo[];
        links: any;
        current_page: number;
        last_page: number;
    };
    flash?: {
        success?: string;
    };
}

export default function Index({ servidores, flash }: ServidorEfetivoIndexProps) {
    const handleDelete = (pesId: number) => {
        if (confirm('Tem certeza que deseja excluir este servidor efetivo?')) {
            router.delete(route('servidores.efetivo.destroy', pesId));
        }
    };

    return (
        <>
            <Head title="Servidores Efetivos" />

            <div className="container mx-auto py-8">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-semibold">Servidores Efetivos</h1>
                    <Link
                        href={route('servidores.efetivo.create')}
                        className="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded"
                    >
                        Adicionar Servidor
                    </Link>
                </div>

                {flash?.success && (
                    <div className="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                        {flash.success}
                    </div>
                )}

                <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead className="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matrícula</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Nascimento</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sexo</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {servidores.data.map((servidor) => (
                                <tr key={servidor.pes_id}>
                                    <td className="px-6 py-4 whitespace-nowrap">{servidor.pessoa.pes_nome}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">{servidor.se_matricula}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {new Date(servidor.pessoa.pes_data_nascimento).toLocaleDateString('pt-BR')}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {servidor.pessoa.pes_sexo === 'M' ? 'Masculino' : 'Feminino'}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            href={route('servidores.efetivo.show', servidor.pessoa.pes_id)}
                                            className="text-blue-500 hover:text-blue-700 mr-4"
                                        >
                                            Ver
                                        </Link>
                                        <Link
                                            href={route('servidores.efetivo.edit', servidor.pessoa.pes_id)}
                                            className="text-yellow-500 hover:text-yellow-700 mr-4"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(servidor.pessoa.pes_id)}
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
                        {servidores.links && (
                            <div className="flex justify-between">
                                {servidores.current_page > 1 && (
                                    <Link
                                        href={route('servidores.efetivo.index', { page: servidores.current_page - 1 })}
                                        className="text-blue-500"
                                    >
                                        Anterior
                                    </Link>
                                )}

                                <span>
                                    Página {servidores.current_page} de {servidores.last_page}
                                </span>

                                {servidores.current_page < servidores.last_page && (
                                    <Link
                                        href={route('servidores.efetivo.index', { page: servidores.current_page + 1 })}
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