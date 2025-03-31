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

interface ServidorTemporario {
    pes_id: number;
    st_data_admissao: string;
    st_data_demissao: string | null;
    pessoa: Pessoa;
}

interface ServidorTemporarioIndexProps extends PageProps {
    servidores: {
        data: ServidorTemporario[];
        links: any;
        current_page: number;
        last_page: number;
    };
    flash?: {
        success?: string;
    };
}

export default function Index({ servidores, flash }: ServidorTemporarioIndexProps) {
    const handleDelete = (pesId: number) => {
        if (confirm('Tem certeza que deseja excluir este servidor temporário?')) {
            router.delete(route('servidores.temporario.destroy', pesId), {
                onSuccess: () => {
                    console.log('Servidor excluído com sucesso');
                },
                onError: (errors) => {
                    console.error('Erro ao excluir servidor:', errors);
                }
            });
        }
    };

    return (
        <>
            <Head title="Servidores Temporários" />

            <div className="container mx-auto py-8">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-semibold">Servidores Temporários</h1>
                    <Link
                        href={route('servidores.temporario.create')}
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
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Admissão</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Demissão</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sexo</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {servidores.data.map((servidor) => (
                                <tr key={servidor.pes_id}>
                                    <td className="px-6 py-4 whitespace-nowrap">{servidor.pessoa.pes_nome}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {new Date(servidor.st_data_admissao).toLocaleDateString('pt-BR')}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {servidor.st_data_demissao 
                                            ? new Date(servidor.st_data_demissao).toLocaleDateString('pt-BR') 
                                            : '-'}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {servidor.pessoa.pes_sexo === 'M' ? 'Masculino' : 'Feminino'}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            href={route('servidores.temporario.show', servidor.pes_id)}
                                            className="text-blue-500 hover:text-blue-700 mr-4"
                                        >
                                            Ver
                                        </Link>
                                        <Link
                                            href={route('servidores.temporario.edit', servidor.pes_id)}
                                            className="text-yellow-500 hover:text-yellow-700 mr-4"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(servidor.pes_id)}
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
                                        href={route('servidores.temporario.index', { page: servidores.current_page - 1 })}
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
                                        href={route('servidores.temporario.index', { page: servidores.current_page + 1 })}
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
