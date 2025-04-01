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
    pessoa_enderecos?: any[];
    pessoa_lotacoes?: any[];
    enderecos: Array<{
        end_id: number;
        end_tipo_logradouro: string;
        end_logradouro: string;
        end_numero: number | null;
        end_bairro: string;
        cidade: {
            cid_id: number;
            nome: string;
        };
    }>;
}

interface ShowProps extends PageProps {
    servidor: ServidorEfetivo;
}

export default function Show({ servidor }: ShowProps) {
    return (
        <>
            <Head title={`Servidor: ${servidor.pessoa.pes_nome}`} />

            <div className="container mx-auto py-8">
                <div className="max-w-4xl mx-auto">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold">Detalhes do Servidor Efetivo</h1>
                        <div className="flex space-x-4">
                            <Link
                                href={route('servidores.efetivo.edit', servidor.pes_id)}
                                className="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded"
                            >
                                Editar
                            </Link>
                            <Link
                                href={route('servidores.efetivo.index')}
                                className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                            >
                                Voltar
                            </Link>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-xl font-semibold">Informações Pessoais</h2>
                        </div>
                        <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome:</p>
                                <p className="font-medium">{servidor.pessoa.pes_nome}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Matrícula:</p>
                                <p className="font-medium">{servidor.se_matricula}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Nascimento:</p>
                                <p className="font-medium">{new Date(servidor.pessoa.pes_data_nascimento).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Sexo:</p>
                                <p className="font-medium">{servidor.pessoa.pes_sexo === 'M' ? 'Masculino' : 'Feminino'}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome da Mãe:</p>
                                <p className="font-medium">{servidor.pessoa.pes_mae}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome do Pai:</p>
                                <p className="font-medium">{servidor.pessoa.pes_pai}</p>
                            </div>
                        </div>
                    </div>

                    {servidor.enderecos && servidor.enderecos.length > 0 ? (
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <h2 className="text-xl font-semibold">Endereços</h2>
                                <Link
                                    href={route('pessoa.endereco.create', servidor.pes_id)}
                                    className="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded text-sm"
                                >
                                    Adicionar Endereço
                                </Link>
                            </div>
                            <div className="p-6">
                                {servidor.enderecos.map((endereco, index) => (
                                    <div key={endereco.end_id} className="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                        <div className="flex justify-between items-start mb-2">
                                            <p className="font-medium">Endereço {index + 1}</p>
                                            <div className="flex space-x-2">
                                                <Link
                                                    href={route('endereco.edit', endereco.end_id)}
                                                    className="text-yellow-500 hover:text-yellow-700"
                                                >
                                                    Editar
                                                </Link>
                                                <button
                                                    onClick={() => {
                                                        if (confirm('Tem certeza que deseja remover este endereço?')) {
                                                            router.delete(route('pessoa.endereco.remove', [servidor.pes_id, endereco.end_id]));
                                                        }
                                                    }}
                                                    className="text-red-500 hover:text-red-700"
                                                >
                                                    Remover
                                                </button>
                                            </div>
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p className="text-gray-500 dark:text-gray-400 text-sm">Endereço completo:</p>
                                                <p>{endereco.end_tipo_logradouro} {endereco.end_logradouro}, {endereco.end_numero || 'S/N'}</p>
                                                <p>{endereco.end_bairro}</p>
                                            </div>
                                            <div>
                                                <p className="text-gray-500 dark:text-gray-400 text-sm">Cidade:</p>
                                                <p>{endereco.cidade?.nome || 'Não informada'}</p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ) : (
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <h2 className="text-xl font-semibold">Endereços</h2>
                                <Link
                                    href={route('pessoa.endereco.create', servidor.pes_id)}
                                    className="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded text-sm"
                                >
                                    Adicionar Endereço
                                </Link>
                            </div>
                            <div className="p-6">
                                <p className="text-gray-500 dark:text-gray-400">Nenhum endereço cadastrado.</p>
                            </div>
                        </div>
                    )}

                    {servidor.pessoa_lotacoes && servidor.pessoa_lotacoes.length > 0 && (
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 className="text-xl font-semibold">Lotações</h2>
                            </div>
                            <div className="p-6">
                                {servidor.pessoa_lotacoes?.map((lotacao, index) => (
                                    <div key={index} className="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                        <p className="font-medium mb-2">Lotação {index + 1}</p>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p className="text-gray-500 dark:text-gray-400 text-sm">Unidade:</p>
                                                <p>{lotacao.unidade?.uni_nome || 'Não informada'}</p>
                                            </div>
                                            <div>
                                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Início:</p>
                                                <p>{lotacao.data_inicio ? new Date(lotacao.data_inicio).toLocaleDateString() : 'Não informada'}</p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
